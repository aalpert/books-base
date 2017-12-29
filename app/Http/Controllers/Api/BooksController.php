<?php

namespace App\Http\Controllers\Api;

use App\Book;
use App\Http\Resources\BookCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BooksController extends Controller
{
    public function index(Request $request)
    {

//        return json_encode(Auth::user());
        $book = Book::with('authors')->with('categories')->paginate(5);
        return new BookCollection($book);
    }
}
