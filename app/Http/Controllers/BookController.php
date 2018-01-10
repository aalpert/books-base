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
        $books = Book::filter(request(['title', 'isbn', 'availability']))->paginate(50);
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
//            'price' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'pages' => 'integer',
            'year' => 'integer',
            'isbn' => 'required',
            'publisher' => 'required',
        ]);

        $book = new Book;
        $raw = request(['title', 'isbn', 'description', 'format', 'year', 'pages', 'source', 'publisher', 'series', 'availability', 'bookbinding']);
//        dd(request('price'));
        $book->prepare($raw)->save();

        if (is_array(request('price'))) {
            $book->updatePrices(request('price'));
        }

        $book->attach(request(['author', 'category', 'publisher']));

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
        $prices = [];
        $sources = Source::all();
        foreach($sources as $source) {
            $prices[$source->id] = $book->prices()->where('source_id', $source->id)->pluck('price')->first();
        }
        return view('pages.books.edit', compact('book', 'sources', 'prices'));
    }

    /**
     * Store book in DB and prepare all relationships
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->validate(request(), [
            'title' => 'required|max:255',
            'pages' => 'integer',
            'year' => 'integer',
            'isbn' => 'required',
        ]);

        $book = Book::findOrFail(request('id'));
        $raw = request(['title', 'isbn', 'price', 'description', 'format', 'year', 'pages', 'source', 'publisher', 'series', 'availability', 'bookbinding']);
        $raw['image'] = $book->image;
        $book->prepare($raw)->update();

        $book->attach(request(['author', 'category', 'publisher']));

        if (is_array(request('price'))) {
            $book->updatePrices(request('price'));
        }

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
        return response()->file(storage_path() . '/app/images/books/' . $book->image);
    }

    public function show(Book $book)
    {
        return view('pages.books.show', compact('book'));
    }
}
