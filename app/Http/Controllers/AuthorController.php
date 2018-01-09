<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('books')->orderBy('books_count', 'desc')->paginate(50);
        return view('pages.authors.list', compact('authors'));
    }

    public function show(Author $author)
    {
        $books = $author->books()->filter(request(['title', 'isbn', 'availability']))->paginate(50);
        return view('pages.authors.show', compact('author', 'books'));
    }
}
