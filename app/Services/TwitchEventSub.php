<?php

namespace App\Services;

use App\Models\EventSub;
use App\Exceptions\TwitchScraperException;
use App\Models\Scope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchEventSub extends TwitchApiReference
{
    /**
     * The batch ID.
     *
     * @var int
     */
    protected static int $batch;

    /**
     * @var array
     */
    protected static array $data = [];

    /**
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws TwitchScraperException
     * @throws CurlException
     */
    public static function scrap(): void
    {
        self::$batch = getBatch(EventSub::class);
        $contents = file_get_contents('https://dev.twitch.tv/docs/eventsub/eventsub-reference/');
//        static::getConditions($contents);
//        static::getEvents($contents);
        static::getSubscriptionTypes($contents);
        static::syncData();
    }

    /**
     * Synchronize data with the database.
     *
     * @return void
     */
    protected static function syncData(): void
    {
        foreach (self::$data as $name => $attributes) {
            $scopeIDs = [];
            foreach ($attributes['scopes'] as $scope) {
                $scopeIDs[] = Scope::firstOrCreate(['name' => $scope])->getKey();
            }
            unset($attributes['scopes']);

            if (empty($attributes['title'])) {
                dd($name);
            }

            $eventSub = EventSub::updateOrCreate(['name' => $name], $attributes);
            $eventSub->scopes()->sync($scopeIDs);
        }
    }

    /**
     * @throws CircularException
     * @throws ChildNotFoundException
     * @throws StrictException
     * @throws CurlException
     * @throws NotLoadedException
     * @throws TwitchScraperException
     */
    protected static function getSubscriptionTypes(string $contents): void
    {
        $dom = new Dom();
        $dom->loadFromUrl('https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/',
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
        );;

        /* @var HtmlNode $contents */
        $contents = ($dom->find('.text-content'))->toArray()[0];
        /* @var array<HtmlNode> $children */
        $children = $contents->getChildren();

        $case = $name = $subCase = null;

        foreach ($children as $child) {
            $tag = $child->getTag()->name();

            if (in_array($tag, ['h1', 'h2'])) {
                $case = $child->innerhtml;
                continue;
            }

            if ($case == 'Subscription Types') {
                $tbody = $child->getChildren()[1];
                /* @var array<HtmlNode> $rows */
                $rows = $tbody->getChildren();
                foreach ($rows as $row) {
                    /* @var array<HtmlNode> $columns */
                    $columns = $row->getChildren();
                    $title = last($columns[0]->getChildren())->innerhtml;
                    $name = strip_tags($columns[1]->innerhtml);

                    static::$data[$name]['title'] = $title;
                    static::$data[$name]['version'] = strip_tags($columns[2]->innerhtml);
                    static::$data[$name]['description'] = $columns[3]->innerhtml;
                    static::$data[$name]['authorization'] = null;
                    static::$data[$name]['request_body'] = null;
                    static::$data[$name]['scopes'] = [];
                }
                continue;
            }

            if ($case == 'Public Beta Program') {
                continue;
            }

            if ($tag == 'h3') {
                $text = $child->innerhtml;

                if ($text == 'Authorization') {
                    $subCase = 'Authorization';
                    continue;
                }

                if ($text == 'Request Body') {
                    continue;
                }

                if (in_array($text, ['Goal End Notification Payload', 'Goal End Webhook Example'])) {
                    continue;
                }

                if (str_ends_with($text, 'Request Body')) {
                    $subCase = 'Request Body';
                    continue;
                }

                if ($text == 'Goal End Notification Example') {
                    continue;
                }

                $subCase = null;
                $name = strip_tags($text);
                continue;
            }

            if ($tag == 'table' && $subCase == 'Request Body') {
                static::$data[$name]['request_body'] = static::handleRequestBody($child);
                $subCase = null;
            }

            if ($tag == 'p') {
                if ($subCase == 'Authorization') {
                    $formatted = formatTextHtmlNode($child, true);

                    static::$data[$name]['authorization'] = trim(data_get(static::$data, $name.'.authorization', '')."\n".$formatted['text']);
                    static::$data[$name]['scopes'] = array_merge(data_get(static::$data, $name.'.scopes', []), $formatted['scopes']);

                    continue;
                }

                static::$data[$name]['instruction'] = formatTextHtmlNode($child);
            }
        }
    }

    /**
     * @param HtmlNode $node
     *
     * @throws CircularException
     * @throws TwitchScraperException
     * @return array
     */
    protected static function handleRequestBody(HtmlNode $node): array
    {
        $data = static::handleRequestTable($node);

        return Arr::mapWithKeys($data, function (array $item, string $key) {
            $newItem = [];
            foreach ($item as $itemKey => $itemValue) {
                if (str_contains($itemValue, 'href="/')) {
                    $itemValue = str_replace(
                        ['href="/', '">'],
                        ['href="https://dev.twitch.tv/', '" target="_blank">'],
                        $itemValue);
                }

                $newItem[$itemKey] = $itemValue;
            }

            return [strip_tags($key) => $newItem];
        });
    }

    /**
     * @param string $contents
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws NotLoadedException
     * @throws StrictException
     * @throws TwitchScraperException
     * @noinspection PhpUnused
     */
    protected static function getConditions(string $contents): void
    {
        $contents = explode('<h2 id="conditions">Conditions</h2>', $contents)[1];
        $contents = explode('<h2 id="emotes">Emotes</h2>', $contents)[0];
        $contents = trim($contents);

        static::handleReference($contents, 'Condition');
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws StrictException
     * @throws TwitchScraperException
     * @noinspection PhpUnused
     */
    protected static function getEvents(string $contents): void
    {
        $contents = explode('<h2 id="events">Events</h2>', $contents)[1];
        $contents = explode('<h2 id="global-cooldown">Global Cooldown</h2>', $contents)[0];
        $contents = trim($contents);

        static::handleReference($contents, 'Event');
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     * @throws CircularException
     * @throws TwitchScraperException
     * @throws StrictException
     */
    protected static function handleReference(string $contents, string $method): void
    {
        $dom = new Dom();
        $dom->loadStr($contents,
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

        $children = $dom->getChildren();

        foreach ($children as $child) {
            $tag = $child->getTag()->name();

            if ($tag == 'h3') {
                $name = $child->getAttribute('id');
                $name = explode('-', $name);
                array_pop($name);
                $name = implode('-', $name);
                $name = Str::slug($name, '.');


                continue;
            }

            if ($child instanceof TextNode) {
                continue;
            }

            $tbody = $child->getChildren();
            if (!isset($tbody[1])) {
                continue;
            }
            /* @var HtmlNode|TextNode $tbody */
            $tbody = $tbody[1];

            if ($tbody instanceof TextNode) {
                continue;
            }

            /* @var array<HtmlNode|TextNode> $rows */
            $rows = $tbody->getChildren();

            foreach ($rows as $row) {
                if (!isset($name)) {
                    throw new TwitchScraperException('getConditions deprecated!');
                }
                if ($row instanceof TextNode) {
                    continue;
                }

                /* @var array<HtmlNode> $columns */
                $columns = $row->getChildren();
                self::{'handle'.$method.'Row'}($name, $columns);
            }
        }
    }

    /**
     * @param string $name
     * @param array  $columns
     *
     * @return void
     * @noinspection PhpUnused
     */
    protected static function handleConditionRow(string $name, array $columns): void
    {
        self::$data[$name]['condition'] = [
            //'name' => strip_tags($columns[0]->innerhtml),
            'type' => strip_tags($columns[1]->innerhtml),
            'required' => $columns[2]->innerhtml == 'yes',
            'description' => strip_tags($columns[3]->innerhtml),
        ];
    }

    /**
     * @param string $name
     * @param array  $columns
     *
     * @return void
     * @noinspection PhpUnused
     */
    protected static function handleEventRow(string $name, array $columns): void
    {
        self::$data[$name]['event'] = [
            //'name' => strip_tags($columns[0]->innerhtml),
            'type' => strip_tags($columns[1]->innerhtml),
            'description' => strip_tags($columns[2]->innerhtml),
        ];
    }
}
