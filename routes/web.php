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

Route::get('/', function () {
    return view('main');
});
Route::get('search/{params?}', 'SearchController@search');
Route::get('signin', 'SigninController@index');

//Route::get('search', 'SearchController@index');
/*Route::get('/search', function() {
    return redirect()->route(
        'search.index',
        ['params' => 'test2']
    );
    //return Redirect::route('search/search', \Illuminate\Support\Facades\Input::query());
});*/

Route::get('test', function () {
    return view('test');
});

Route::post('/endpoint', function() {
    /**
     * endpoint: https://mp1180ms5a.execute-api.us-west-2.amazonaws.com/Prod/search
     * Body: {"title":"test"}
     * Raw
     **/
});
Route::get('searchWithPaginate', 'SearchController@searchWithPaginate');