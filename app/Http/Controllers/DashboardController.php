<?php

namespace App\Http\Controllers;

use App\Author;
use App\Category;
use App\Publisher;
use App\Series;
use Illuminate\Http\Request;
use App\Book;

class DashboardController extends Controller
{
    public function index()
    {
        $books = Book::count();
        $authors = Author::count();
        $categories = Category::count();
        $series = Series::count();
        $publisers = Publisher::count();
        return view('pages/dashboard', compact('books', 'authors', 'categories', 'series', 'publisers'));
    }
}
