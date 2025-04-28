<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/shows', function () {
    return view('shows');
});

Route::get('/movies', function () {
    return view('movies');
});

Route::get('/webseries', function () {
    return view('webseries');
});

Route::get('/new', function () {
    return view('new');
});

Route::get('/mylist', function () {
    return view('mylist');
});