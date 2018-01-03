<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Source;

class BookController extends Controller
{
    /**
     * List all books
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $books = Book::paginate(50);
        return view('pages.books.list', compact('books'));
    }

    public function create()
    {
        $sources = Source::all();
        return view('pages.books.create', compact('sources'));
    }

    /**
     * Store book in DB and prepare all relationships
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(), [
            'title' => 'required|max:255',
            'source' => 'required',
            'price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'pages' => 'integer',
            'year' => 'integer',
            'isbn' => 'required',
            'publisher' => 'required',
        ]);

        $book = new Book;
        $raw = request(['title', 'isbn', 'price', 'description', 'format', 'year', 'pages', 'source', 'publisher', 'series']);
        $book->prepare($raw)->save();

        $book->attach(request(['author', 'category']));

        session()->flash('success_message', 'Книга добавлена');
        return redirect()->route('book.list');
    }

    /**
     * Delete a book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete()
    {
        // Find the Book
        $book = Book::findOrFail(request('id'));
        // Delete the source
        $book->delete();
        // Go back to Sources list with success message
        session()->flash('success_message', 'Книга удалена');
        return redirect()->route('book.list');
    }

    /**
     * @param Book $book
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Book $book)
    {
        $sources = Source::all();
        return view('pages.books.edit', compact('book', 'sources'));
    }

    /**
     * Store book in DB and prepare all relationships
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->validate(request(), [
            'title' => 'required|max:255',
            'source' => 'required',
            'price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'pages' => 'integer',
            'year' => 'integer',
            'isbn' => 'required',
            'publisher' => 'required',
        ]);

        $book = Book::findOrFail(request('id'));
        $raw = request(['title', 'isbn', 'price', 'description', 'format', 'year', 'pages', 'source', 'publisher', 'series']);
        $raw['image'] = $book->image;
        $book->prepare($raw)->update();

        $book->attach(request(['author', 'category']));

        session()->flash('success_message', 'Книга обновлена');
        return redirect()->route('book.list');
    }

    /**
     * Return Book image
     * @param Book $book
     * @return mixed
     */
    public function gallery(Book $book)
    {
        return response()->file(storage_path() . '/app/images/covers/' . $book->image);
    }

    public function show(Book $book)
    {
        return view('pages.books.show', compact('book'));
    }
}
