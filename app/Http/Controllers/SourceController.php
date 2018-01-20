<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Source;

class SourceController extends Controller
{
    /**
     * Show sources list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $sources = Source::all();
        return view('pages.sources.list', compact('sources'));
    }

    /**
     * Show Create form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('pages.sources.create');
    }

    /**
     * Save the Source in Database
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        // Validate the request
        $this->validate(request(), [
            'title' => 'required|max:255'
        ]);

        // Save the Source
        $source = new Source;
        $source->title = request('title');
        $source->driver = request('driver');
        $source->save();

        // Go back to Sources list with success message
        session()->flash('success_message', 'Новый источник добавлен');
        return redirect()->route('source.list');
    }


    /**
     * Delete the source
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete()
    {
        // Find the Source
        $source = Source::findOrFail(request('id'));
        // Delete the source
        $source->delete();
        // Go back to Sources list with success message
        session()->flash('success_message', 'Источник удален');
        return redirect()->route('source.list');
    }

    /**
     * Show edit form
     * @param Source $source
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Source $source)
    {
        return view('pages.sources.edit', compact('source'));
    }

    /**
     * Save the edits to Database
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        // Validate the request
        $this->validate(request(), [
            'title' => 'required|max:255'
        ]);

        // Save the Source
        $source = Source::findOrFail(request('id'));
        $source->title = request('title');
        $source->save();

        // Go back to Sources list with success message
        session()->flash('success_message', 'Источник был изменен');
        return redirect()->route('source.list');
    }

    /**
     * Shows all books that have this price
     * @param Source $source
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Source $source) {
        $books = Book::withSource($source->id)->filter(request(['title', 'isbn', 'availability']))->paginate(50);
        return view('pages.sources.show', compact('source', 'books'));
    }
}
