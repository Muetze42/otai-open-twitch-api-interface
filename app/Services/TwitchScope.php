<?php

namespace App\Services;

use App\Exceptions\TwitchScraperException;
use App\Models\Scope;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\CurlException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class TwitchScope
{
    /**
     * The batch ID.
     *
     * @var int
     */
    protected static int $batch;

    /**
     * @throws CurlException
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws TwitchScraperException
     */
    public static function scrap(): void
    {
        self::$batch = getBatch(Scope::class);

        $dom = new Dom();
        $dom->loadFromUrl('https://dev.twitch.tv/docs/authentication/scopes/',
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

        $tables = ($dom->find('table'))->toArray();

        if (count($tables) != 2) {
            throw new TwitchScraperException('Wanted 2 tables. Get ' . count($tables) . '.');
        }

        static::handleTable($tables[0]);
        static::handleTable($tables[1], true);
    }

    /**
     * @throws CircularException
     * @throws TwitchScraperException
     */
    public static function handleTable(HtmlNode $node, $chatAndPupSub = false): void
    {
        /* @var HtmlNode $tbody */
        $tbody = $node->getChildren()[1];
        /* @var array<HtmlNode> $rows */
        $rows = $tbody->getChildren();

        foreach ($rows as $row) {
            $columns = $row->getChildren();

            Scope::updateOrCreate(
                ['name' => strip_tags($columns[0]->innerhtml)],
                [
                    'description' => formatTextHtmlNode($columns[1]),
                    'chat_and_pup_sub' => $chatAndPupSub,
                    'batch' => self::$batch,
                ]
            );
        }
    }
}
