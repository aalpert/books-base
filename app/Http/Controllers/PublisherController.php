<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Publisher;

class PublisherController extends Controller
{
    public function index()
    {
        $publishers = Publisher::withCount('books')->orderBy('books_count', 'desc')->paginate(50);
        return view('pages.publishers.list', compact('publishers'));
    }

    public function show(Publisher $publisher)
    {
        $books = $publisher->books()->filter(request(['title', 'isbn', 'availability']))->paginate(50);
        return view('pages.publishers.show', compact('publisher', 'books'));
    }
}
