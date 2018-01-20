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
        $imports = Import::orderBy('created_at', 'desc')->get();
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
            'params' => [
                'filename' => 'storage/app/' . $path,
                'limit_publishers' => !empty(request('publishers', '')) ? explode('||', request('publishers')) : '',
            ],
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
        return response()->download(storage_path() . '/app/pricelist/' . substr($import->params['filename'], strrpos($import->params['filename'], '/') + 1));
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

    /**
     * Just a quick playground
     */
    public function sandbox() {
        $reference = 'http://www.trade.bookclub.ua/books/product.html?id=46736';
        @$html = file_get_contents($reference);
        if (empty($html)) {
            dd('fail load reference page');
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXpath($dom);

        $node = $xpath->query('//div[@class="goods-descrp"]');
        if (count($node)) {
            $book['description'] = (nl2br(trim($node[0]->nodeValue)));
        }

        //params
//        $node = $xpath->query('//ul[@class="goods-short"]');
//        $pars = nl2br($node[0]->nodeValue);
//        $book['details']['']\App\Import\Ksd::extract($pars, 'Вес:', '<br');

        //image
        $node = $xpath->query('//div[@class="goods-image"]');
        if (count($node)) {
            $img = $node[0]->getElementsByTagName('img');
            if (count($img)) {
                $src = str_replace('/b/', '/', $img[0]->getAttribute('src'));
                $src = str_replace('_b.', '.', $src);
            }
        }

        // Author
        $node = $xpath->query('//div[@class="autor-text-color"]');
        if (count($node)) {
            $name = trim($node[0]->getElementsByTagName('h2')[0]->nodeValue);
            $descr = trim($node[0]->getElementsByTagName('p')[0]->nodeValue);
        }

        $node = $xpath->query('//div[@class="autor-image"]');
        if (count($node)) {
            $img = $node[0]->getElementsByTagName('img');
            if (count($img)) {
                $src = $img[0]->getAttribute('src');
                dd($src);
            }
        }
    }


}
