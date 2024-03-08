<?php

use App\Exceptions\TwitchScraperException;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\TextNode;
use PHPHtmlParser\Exceptions\CircularException;

if (!function_exists('formatTextHtmlNode')) {
    /**
     * @param HtmlNode $node
     * @param bool     $withScopes
     * @param string   $baseUrl
     * @throws CircularException
     * @throws TwitchScraperException
     * @return array|string
     */
    function formatTextHtmlNode(
        HtmlNode $node,
        bool $withScopes = false,
        string $baseUrl = 'https://dev.twitch.tv'
    ): array|string {
        $scopes = [];
        $text = '';

        foreach ($node->getChildren() as $textPart) {
            if ($textPart instanceof TextNode) {
                $text .= $textPart->innerhtml;
                continue;
            }
            if ($textPart instanceof HtmlNode) {
                $tag = $textPart->getTag()->name();

                if ($tag == 'a') {
                    $url = $textPart->getAttribute('href');
                    if (!str_starts_with($url, 'http')) {
                        $url = $baseUrl . $url;
                    }
                    $text .= '<a href="' . $url . '" target="_blank">' . $textPart->innerhtml . '</a>';
                    continue;
                }

                if ($tag == 'strong') {
                    if (str_contains($textPart->innerhtml, ':') && strtolower($textPart->innerhtml) === $textPart->innerhtml) {
                        $scopes[] = $textPart->innerhtml;
                    }
                    $text .= '<strong>' . $textPart->innerhtml . '</strong>';
                    continue;
                }

                if ($tag == 'p') {
                    $text .= $text ? '<p>' . $textPart->innerhtml . '</p>' : $textPart->innerhtml;
                    continue;
                }

                if ($tag == 'ul') {
                    $text .= "\n<ul>" . formatTextHtmlNode($textPart) . "</ul>\n";
                    continue;
                }

                if ($tag == 'li') {
                    $text.= "\n<li>".formatTextHtmlNode($textPart)."</li>\n";
                    continue;
                }

                if ($tag == 'code' && $textPart->getAttribute('class') == 'highlighter-rouge' &&
                    str_contains($textPart->innerhtml, ':')) {
                    $scopes[] = $textPart->innerhtml;
                }

                if (in_array($tag, ['code', 'em', 'span', 'span', 'i', 'b'])) {
                    $attribute = '';
                    $class = $textPart->getAttribute('class');
                    if ($class) {
                        $attribute = ' class="' . $class . '"';
                    }

                    $text .= '<' . $tag . $attribute . '>' . $textPart->innerhtml . '</' . $tag . '>';
                    continue;
                }

                if ($tag == 'br') {
                    $text .= "\n<br>";
                    continue;
                }

                throw new TwitchScraperException('Unexpected child node tag `' . $tag . '`');
            }
        }

        $output = [
            'text' => trim($text),
            'scopes' => $scopes,
        ];

        if ($withScopes) {
            return $output;
        }

        return $output['text'];
    }
}

if (!function_exists('errorImage')) {
    /**
     * Choose an error image
     *
     * @param int $errorCode
     * @return string
     */
    function errorImage(int $errorCode): string
    {
        $errorImages = [
            '401' => '403.svg',
            '403' => '403.svg',
            '404' => '404.svg',
            '500' => '503.svg',
        ];

        return $errorImages[$errorCode] ?? '404.svg';
    }
}

if (!function_exists('sentenceNotEnded')) {
    /**
     * @param string $sentence
     * @return string
     */
    function sentenceNotEnded(string $sentence): string
    {
        if (!in_array(substr($sentence, -1), ['.', '!', '?'])) {
            return $sentence;
        }

        return substr($sentence, 0, -1);
    }
}

