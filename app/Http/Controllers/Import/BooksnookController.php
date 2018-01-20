<?php

namespace App\Http\Controllers\Import;

use App\Import;
use App\Jobs\ImportRemove;
use App\Source;
use App\Http\Controllers\Controller;

class BooksnookController extends Controller
{
    public function create()
    {
        $sources = Source::all();
        return view('pages.import.booksnook.create', compact('sources'));
    }

    public function store()
    {
        $import = Import::create([
            'source_id' => request('source'),
            'params' => [
                'host' => request('host'),
                'token' => request('token', ''),
                'limit_publishers' => !empty(request('publishers', '')) ? explode('||', request('publishers')) : '',
            ],
            'clear' => request('clear'),
        ]);

        \App\Jobs\Import::withChain([new ImportRemove($import)])->dispatch($import);

        session()->flash('success_message', 'Импортирован');
        return redirect()->route('import.list');
    }
}
