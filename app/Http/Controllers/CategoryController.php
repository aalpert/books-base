<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('books')->orderBy('books_count', 'desc')->paginate(50);
        return view('pages.categories.list', compact('categories'));
    }

    public function show(Category $category)
    {
        $books = $category->books()->filter(request(['title', 'isbn', 'availability']))->paginate(50);
        return view('pages.categories.show', compact('category', 'books'));
    }
}
