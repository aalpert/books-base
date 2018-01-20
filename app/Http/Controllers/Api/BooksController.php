<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\Http\Resources\BookCollection;
use App\Publisher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BooksController extends Controller
{
    /**
     * Get all books by param
     * @param Request $request
     * @return BookCollection
     */
    public function index(Request $request)
    {
        $books=Book::distinct()
            ->with('series')
            ->with('publishers')
            ->where('availability', '<>', 'NVN');
        if (!empty(request('publishers'))) {
            $books->whereHas('publishers', function ($q) {
                $q->where('title', request('publishers'));
            });
        }

        if (!empty(request('series'))) {
            $books->whereHas('series', function ($q) {
                $q->where('title', request('series'));
            });
        }

        $books = $books->paginate(request('pp', 50));
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
            return response()->file(storage_path() . '/app/images/items/' . $image);
        }
        return response('Not Found', 404);
    }
}
