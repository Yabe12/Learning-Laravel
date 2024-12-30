<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function(){
    return view('home');
});
Route::get('/greet/{name}', function ($name) {
    return view('greet',['name' => $name]);
});
Route::get('/user/{id}', function ($id) {
    return view('user',['id' => $id]);
});