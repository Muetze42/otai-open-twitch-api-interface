<?php

namespace App\Services;

use App\Exceptions\TwitchScraperException;
use App\Models\Api;
use App\Models\Endpoint;
use App\Models\Scope;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchApiReference
{
    /**
     * Twitch API Reference data array.
     *
     * @var array
     */
    protected static array $data = [];

    /**
     * Defaults initial data in every array item.
     *
     * @var array
     */
    protected static array $defaults = [
        'request_body' => [],
        'request_query_parameters' => [],
        'response_body' => [],
        'response_codes' => [],
    ];

    /**
     * The batch ID.
     *
     * @var int
     */
    protected static int $batch;

    /**
     * Scrap API reference from Twitch Dev Docs and save to database.
     *
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     * @throws CurlException
     * @throws TwitchScraperException
     */
    public static function scrap(): void
    {
        self::$batch = getBatch(Endpoint::class);

        $dom = new Dom();
        $dom->loadFromUrl('https://dev.twitch.tv/docs/api/reference/',
            [
                'whitespaceTextNode' => false,
                'strict' => false,
                'enforceEncoding' => null,
                'cleanupInput' => true,
                'removeScripts' => true,
                'removeStyles' => true,
                'preserveLineBreaks' => false,
                'removeDoubleSpace' => true,
                'removeSmartyScripts' => true,
                'depthFirstSearch' => false,
                'htmlSpecialCharsDecode' => false,
            ]
        );

        /* @var array<HtmlNode> $contents */
        $contents = ($dom->find('.doc-content'))->toArray();

        foreach ($contents as $content) {
            $child = $content->firstChild();

            if (!$child instanceof HtmlNode) {
                throw new TwitchScraperException('Wanted class for content is `HtmlNode`');
            }

            static::handleContentChild($child);
        }

        static::syncData();
    }

    /**
     * Synchronize data with the database.
     *
     * @return void
     */
    protected static function syncData(): void
    {
        $api = Api::updateOrCreate(
            ['name' => 'Helix'],
            ['base_url' => 'https://api.twitch.tv/helix']
        );

        foreach (self::$data as $name => $attributes) {
            /* @var Resource $resource */
            $resource = $api->resources()->firstOrCreate(['name' => $attributes['resource']]);
            //$scopeIDs = Scope::whereIn('name', $attributes['scopes'])->pluck('id')->toArray();
            $scopeIDs = [];
            foreach ($attributes['scopes'] as $scope) {
                $scopeIDs[] = Scope::firstOrCreate(['name' => $scope])->getKey();
            }
            unset($attributes['resource']);
            unset($attributes['scopes']);

            /* @var Endpoint $endpoint */
            $endpoint = $resource->endpoints()->updateOrCreate(
                ['name' => $name],
                $attributes
            );

            $endpoint->scopes()->sync($scopeIDs);
        }
    }

    /**
     * @param HtmlNode $node
     * @throws CircularException
     * @throws TwitchScraperException
     */
    protected static function handleContentChild(HtmlNode $node): void
    {
        if ($node->getAttribute('class') != 'left-docs') {
            throw new TwitchScraperException('Wanted element class attribut is `left-docs`. `' . $node->getAttribute('class') . '` given.');
        }

        foreach ($node->getChildren() as $child) {
            if (!$child instanceof HtmlNode) {
                throw new TwitchScraperException('Wanted class for child node is `HtmlNode`');
            }

            if ($child->getTag()->name() == 'h1') {
                static::buildIndex($node);
                return;
            }

            static::handleEndpoint($node);
        }
    }

    /**
     * @param HtmlNode $node
     * @throws CircularException
     * @throws TwitchScraperException
     * @return void
     */
    protected static function handleEndpoint(HtmlNode $node): void
    {
        $name = null;
        $instruction = $authorization = '';
        $case = 'instruction';
        $scopes = [];
        $userAccessTokensAuth = $appAccessTokenAuth = false;

        foreach ($node->getChildren() as $child) {
            if ($child->getTag()->name() == 'h2') {
                $name = $child->innerhtml;
                continue;
            }

            if (!$name) {
                throw new TwitchScraperException('Could`t find node child name.');
            }

            $tagName = $child->getTag()->name();

            if ($tagName == 'a') {
                continue;
            }

            if ($tagName == 'h3') {
                $case = $child->innerhtml;
            }

            if ($tagName == 'p' && $case == 'instruction') {
                $formatted = formatTextHtmlNode($child, true);
                $instruction .= "\n" . $formatted['text'];
                $scopes = array_merge($scopes, $formatted['scopes']);
            }

            if ($tagName == 'blockquote' && $case == 'instruction') {
                $instruction .= "\n";
                $instruction .= '<blockquote>'.formatTextHtmlNode($child).'</blockquote>';
            }

            if ($tagName == 'p' && $case == 'URL') {
                $text = preg_replace('/\s+/', ' ', strip_tags($child->innerhtml));
                $parts = explode(' ', $text);

                $method = isset($parts[1]) ? $parts[0] : 'GET';
                $route = $parts[1] ?? $parts[0];

                self::$data[$name]['method'] = $method;
                self::$data[$name]['route'] = str_replace('https://api.twitch.tv/helix', '', $route);
            }

            if (($tagName == 'p' || $tagName == 'ul') && $case == 'Authorization') {
                $formatted = formatTextHtmlNode($child, true);
                $formattedText = $formatted['text'];

                $authorization.= $tagName == 'ul' && !str_contains($formattedText, '<ul>') ?'<ul>'.$formattedText.'</ul>' : $formattedText;

                $scopes = array_merge($scopes, $formatted['scopes']);

                if (str_contains($child->innerhtml, 'user-access-tokens') || str_contains($child->innerhtml, 'User-Access token') ||
                    str_contains($child->innerhtml, 'Requires OAuth Scope') || str_contains($child->innerhtml, 'user access token')) {
                    $userAccessTokensAuth = true;
                }
                if (str_contains($child->innerhtml, 'app-access-tokens')) {
                    $appAccessTokenAuth = true;
                }
            }
            $replace = [
                'Request Body Parameters' => 'Request Body',
            ];
            if ($case == 'Request Query Parameter') {
                $case = 'Request Query Parameters';
            }
            $case = str_replace(array_keys($replace), array_values($replace), $case);
            if (in_array($case, ['Request Body', 'Request Query Parameters', 'Response Body'])) {
                $slug = Str::slug($case, '_');

                switch ($tagName) {
                    case 'table':
                        self::$data[$name][$slug]['items'] = static::handleRequestTable($child);
                        break;
                    case 'h3':
                        // silent
                        break;
                    case 'p':
                        self::$data[$name][$slug]['text'] = formatTextHtmlNode($child);
                        break;
                    default:
                        throw new TwitchScraperException('Unexpected child node');
                }
            }

            if ($case == 'Response Codes' && $child->getTag()->name() == 'table') {
                $tbody = $child->getChildren()[1];
                /* @var array<HtmlNode> $rows */
                $rows = $tbody->getChildren();
                foreach ($rows as $row) {
                    $columns = $row->getChildren();
                    $code = preg_replace('/\D/', '', $columns[0]->innerhtml);
                    $description = $columns[1]->innerhtml;

                    self::$data[$name]['response_codes'][$code] = $description;
                }
            }
        }

        self::$data[$name] = array_merge(
            self::$data[$name],
            [
                'instruction' => trim($instruction),
                'authorization' => trim($authorization),
                'scopes' => $scopes,
                'user_access_tokens_auth' => $userAccessTokensAuth || count($scopes),
                'app_access_token_auth' => $appAccessTokenAuth,
                'active' => $userAccessTokensAuth,
            ]
        );
    }

    /**
     * @throws TwitchScraperException
     * @throws CircularException
     */
    protected static function handleRequestTable(HtmlNode $node): array
    {
        if ($node->getTag()->name() != 'table') {
            throw new TwitchScraperException('Wanted class for content is `HtmlNode`');
        }

        $children = $node->getChildren();
        $thead = $children[0];
        $tbody = $children[1];

        if ($thead->getTag()->name() != 'thead' || $tbody->getTag()->name() != 'tbody') {
            throw new TwitchScraperException('Wanted class for content is `HtmlNode`');
        }

        $columnCount = count($thead->getChildren()[0]->getChildren());

        /* @var array<HtmlNode> $rows */
        $rows = $tbody->getChildren();

        $items = [];

        $latest = $latestParent = null;

        foreach ($rows as $row) {
            /* @var array<HtmlNode> $columns */
            $columns = $row->getChildren();
            $instructionColumn = $columnCount == 3 ? 2 : 3;
            $name = trim($columns[0]->innerhtml);
            $type = $columns[1]->innerhtml;
            $data = [
                'type' => $type,
                'required' => $columnCount == 3 || strtolower($columns[2]->innerhtml) != 'no',
                'instruction' => formatTextHtmlNode($columns[$instructionColumn]),
            ];

            if (str_starts_with($columns[0]->innerhtml, ' ')) {
                $array[$name] = $data;

                if ($latestParent) {
                    $items[$latest]['children'][$latestParent]['children'] = array_merge(
                        data_get($items, $latest . '.children', []),
                        $array,
                    );
                } else {
                    $items[$latest]['children'] = array_merge(
                        data_get($items, $latest . '.children', []),
                        $array,
                    );
                }

                if ($latest && (str_contains(Str::lower($type), 'object')) || str_contains($type, '[]')) {
                    $latestParent = $name;
                }
            } else {
                $items[$name] = $data;
                $latest = $name;
                $latestParent = null;
            }
        }

        return $items;
    }

    /**
     * @throws CircularException
     * @throws TwitchScraperException
     */
    protected static function buildIndex(HtmlNode $node): void
    {
        /* @var HtmlNode $tableNode */
        $tableNode = $node->getChildren()[1];

        $tbody = $tableNode->getChildren()[1];

        if ($tbody->getTag()->name() != 'tbody') {
            throw new TwitchScraperException('`tbody` wanted. `' . $tbody->getTag()()->name() . '` given.');
        }

        /* @var array<HtmlNode> $rows */
        $rows = $tbody->getChildren();

        foreach ($rows as $row) {
            $columns = $row->getChildren();

            self::$data[strip_tags($columns[1]->innerhtml)] = array_merge(
                [
                    'resource' => $columns[0]->innerhtml,
                    'description' => formatTextHtmlNode($columns[2]),
                    'batch' => self::$batch,
                ],
                self::$defaults
            );
        }
    }
}
