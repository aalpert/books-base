<?php

namespace App;

use Excel;
use Storage;


class ImportBook
{
    private static function exctract($html, $needle, $end = '<BR>')
    {
        if (!stristr($html, $needle)) {
            return null;
        }
        $str = str_after($html, $needle);
        $str = str_before($str, $end);
        $str = str_after($str, '</a>');
        return mb_convert_encoding(trim($str), 'UTF-8', 'UTF-8');
    }

    /**
     * This method is to process the EXMO specific pricelist
     * @param $raw
     * @return array
     */
    public static function process($raw)
    {
        // http://92.39.237.181/Photo/550000/551809.jpg
        $SOURCEURL = 'http://92.39.237.181';
        $reference = (int)$raw->reference;
        // Find out URL
        $reference = $SOURCEURL . '/HTML/' . ((int)($reference / 10000) * 10000) . '/' . $reference . '.html';

        $book = [
            'ref' => $reference,
            'reference' => (int)$raw->reference,
            'title' => trim($raw['title']),
            'author' => trim($raw['author']),
            'isbn' => trim($raw['isbn']),
            'pages' => (int)$raw['pages'],
            'year' => (int)$raw['year'],
            'format' => trim($raw['format']),
            'price' => $raw['price'],
            'category' => null,
            'series' => null,
            'description' => null,
            'image' => null,
            'additional_notes' => $raw['new'],
            'publisher' => trim($raw['publisher']),
        ];

        // Load the content
        @$html = file_get_contents($reference);
        if (empty($html)) {
            return $book;
        }

        $html = iconv('windows-1251', 'utf-8', $html);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        // Get the title
        $book['title'] = (mb_convert_encoding(substr(trim($dom->getElementsByTagName('h1')->item(0)->textContent), 0, 255), 'UTF-8', 'UTF-8'));

        // Get the description
        $book['description'] = (nl2br(trim($dom->getElementsByTagName('div')->item(0)->textContent)));

        $html = trim(preg_replace('/\s+/', ' ', $html));

        // Get the author
        $book['author'] = $e = static::exctract($html, 'Автор:');

        // Get the genre
        $book['category'] = static::exctract($html, 'Жанр:');

        // Get the series
        $book['series'] = static::exctract($html, 'Серия:');

        // Get cover
        $img = $SOURCEURL . trim($dom->getElementsByTagName('img')->item(0)->getAttribute('src'));
        @$contents = file_get_contents($img);
        if (!empty($contents)) {
            $book['image'] = 'book-' . substr($img, strrpos($img, '/') + 1);
            Storage::put('images/covers/' . $book['image'], $contents);
        }

        return $book;
    }

}
