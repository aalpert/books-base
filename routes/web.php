<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/', 'DashboardController@index')->name('main');

    Route::get('/sources', 'SourceController@index')->name('source.list');
    Route::post('/source', 'SourceController@store')->name('source.store');
    Route::delete('/source', 'SourceController@delete')->name('source.delete');;
    Route::patch('/source', 'SourceController@update')->name('source.update');;
    Route::get('/source/create', 'SourceController@create')->name('source.create');
    Route::get('/source/{source}/edit', 'SourceController@edit')->name('source.edit');
    Route::get('/source/{source}', 'SourceController@show')->name('source.show');

    Route::get('/books', 'BookController@index')->name('book.list');
    Route::post('/book', 'BookController@store')->name('book.store');
    Route::delete('/book', 'BookController@delete')->name('book.delete');;
    Route::patch('/book', 'BookController@update')->name('book.update');;
    Route::get('/book/create', 'BookController@create')->name('book.create');
    Route::get('/book/{book}/edit', 'BookController@edit')->name('book.edit');
    Route::get('/book/{book}/gallery', 'BookController@gallery')->name('book.gallery');
    Route::get('/book/{book}', 'BookController@show')->name('book.show');

    Route::get('/import', 'ImportController@index')->name('import.list');
    Route::post('/import', 'ImportController@store')->name('import.store');
    Route::get('/import/create', 'ImportController@create')->name('import.create');
    Route::get('/import/create/booksnook', 'Import\BooksnookController@create')->name('import.create.booksnook');
    Route::post('/import/create/booksnook', 'Import\BooksnookController@store')->name('import.store.booksnook');
    Route::get('/import/{import}', 'ImportController@show')->name('import.show');
    Route::get('/import/{import}/pricelist', 'ImportController@getPriceList')->name('import.getPriceList');
    Route::delete('/import/{import}/clean', 'ImportController@clean')->name('import.clean');

    Route::get('/authors', 'AuthorController@index')->name('author.list');
    Route::get('/author/{author}', 'AuthorController@show')->name('author.show');

    Route::get('/publishers', 'PublisherController@index')->name('publisher.list');
    Route::get('/publisher/{publisher}', 'PublisherController@show')->name('publisher.show');

    Route::get('/series', 'SeriesController@index')->name('series.list');
    Route::get('/series/{series}', 'SeriesController@show')->name('series.show');

    Route::get('/categories', 'CategoryController@index')->name('category.list');
    Route::get('/category/{category}', 'CategoryController@show')->name('category.show');
});


Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login')->name('auth.login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
