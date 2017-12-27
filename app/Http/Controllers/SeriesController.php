<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Series;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::withCount('books')->orderBy('books_count', 'desc')->paginate(50);
        return view('pages.series.list', compact('series'));
    }

    public function show(Series $series)
    {
        $books = $series->books()->paginate(50);
        return view('pages.series.show', compact('series', 'books'));
    }
}
