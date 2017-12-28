<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Book extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $authors = [];
        foreach ($this->authors as $a) {
            $authors[] = [
                'name' => $a['name'],
                'description' => $a['description'],
            ];
        }

        $categories = [];
        foreach ($this->categories as $c) {
            $categories[] = [
                'name' => $c['title'],
            ];
        }

        return [
            // Basic fields
            'title' => $this->title,
            'year' => $this->year,
            'format' => $this->format,
            'pages' => $this->pages,
            'sku' => $this->sku,
            'isbn' => $this->isbn,
            'additional_notes' => $this->additional_notes,

            'publisher' => [
                'title' => $this->publisher['title'],
                'description' => $this->publisher['description']
            ],

            'series' => [
                'title' => $this->series['title'],
            ],

            'authors' => $authors,
            'categories' => $categories,

            'description' => $this->description,
        ];
    }
}
