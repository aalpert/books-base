<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Resources\Book as BookResource;
use App\Http\Resources\BookCollection;
use Illuminate\Http\Request;

class BooksApiController extends Controller
{
    public function index()
    {
        $book = Book::with('authors')->with('categories')->paginate(5);
        return new BookCollection($book);
    }
}
