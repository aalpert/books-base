<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\Http\Resources\BookCollection;
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

        $book = Book::with('authors')->with('categories')->paginate(5);
        return new BookCollection($book);
    }

    /**
     * Get the image file content
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function image()
    {
        $image = Book::where('sku', request('sku'))->pluck('image')->first();
        if ($image) {
            return response()->file(storage_path() . '/app/images/covers/' . $image);
        }
        return response('Not Found', 404);
    }
}
