<?php

namespace App\Http\Controllers;

use App\Jobs\ImportRemove;
use Illuminate\Http\Request;
use App\Source;
use App\Import;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function index()
    {
        $imports = Import::all();
        return view('pages.import.list', compact('imports'));
    }

    public function create()
    {
        $sources = Source::all();
        return view('pages.import.create', compact('sources'));

    }

    public function store(Request $request)
    {
        $filename = request('source') . '_' . date('d-m-Y') . '_' . str_random(40) . '.csv';
        $path = $request->file('pricelist')
            ->storeAs('pricelist', $filename);

        $import = Import::create([
            'source_id' => request('source'),
            'filename' => 'storage/app/' . $path,
            'limit_publishers' => request('publishers'),
            'clear' =>request('clear'),
        ]);

        \App\Jobs\Import::withChain([new ImportRemove($import)])->dispatch($import);


        session()->flash('success_message', 'Импортирован');
        return redirect()->route('import.list');
    }

    public function show(Import $import)
    {
        $q = $import->logs()->where('status', '=', request('status', 'created'));
        $logs = $q->paginate(50);
        return view('pages.import.show', compact('import', 'logs'));
    }


    /**
     * Download the pricelist
     * @param Import $import
     * @return mixed
     */
    public function getPriceList(Import $import)
    {
        return response()->download(storage_path() . '/app/pricelist/' . substr($import->filename, strrpos($import->filename, '/') + 1));
    }

    /**
     * Clear the database
     * @param Import $import
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clean(Import $import)
    {
        $this->dispatch(new ImportRemove($import));
        session()->flash('success_message', 'Очистка базы');
        return redirect()->route('import.list');
    }
}
