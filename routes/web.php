<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/html', function () {
    return '<h1>Hello world</h1>';
});

Route::get('/string', function () {
    return 'hello world';
});

Route::get('/json', function () {
    return ['foo' => 'bar'];
});