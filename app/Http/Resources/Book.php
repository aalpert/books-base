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
                'title' => $c['title'],
            ];
        }

        $publishers = [];
        foreach ($this->publishers as $p) {
            $publishers[] = [
                'title' => $p['title'],
                'description' => $p['description'],
            ];
        }

        $details = array_merge($this->details, [
            'series' => $this->series['title'],
            'authors' => $authors,
            'categories' => $categories,
            'publishers' => $publishers,
        ]);

        return [
            // Basic fields
            'title' => $this->title,
            'image' => $this->image,
            'sku' => $this->sku,
            'year' => $this->year,
            'availability' => $this->availability,
            'price' => $this->prices()->orderBy('price', 'asc')->pluck('price')->first(),
            'description' => $this->description,

            'details' => $details,
        ];
    }
}
