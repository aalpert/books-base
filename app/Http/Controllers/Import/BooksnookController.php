<?php

namespace App\Http\Controllers\Import;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BooksnookController extends Controller
{
    public function create()
    {
        $client = new Client();
        $res = $client->get('http://localhost:8888/booksnook.v1/api/export', ['query' => ['pp' => 100, 'pn' => 9]]);
        dd($res, json_decode($res->getBody()));
    }
}
