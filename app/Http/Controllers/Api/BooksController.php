<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\Http\Resources\BookCollection;
use App\Publisher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BooksController extends Controller
{
    /**
     * Get all books by param
     * @param Request $request
     * @return BookCollection
     */
    public function index(Request $request)
    {
        $books = Book::with('authors')->with('categories')->where('availability', '<>', 'NVN');
        if (!empty(request('publishers', ''))) {
            $publishers = explode('||', request('publishers', ''));
            if (count($publishers)) {
                $publishers = Publisher::whereIn('title', $publishers);
                if ($publishers->count()) {
                    $books->whereIn('publisher_id', $publishers->pluck('id')->all());
                }
            }
        }
        $books = $books->paginate(50);
        return new BookCollection($books);
    }

    /**
     * Get the image file content
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function image($sku)
    {
        $image = Book::where('sku', $sku)->pluck('image')->first();
        if ($image) {
            return response()->file(storage_path() . '/app/images/covers/' . $image);
        }
        return response('Not Found', 404);
    }
}
