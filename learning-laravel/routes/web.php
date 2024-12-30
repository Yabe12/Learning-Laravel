<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function(){
    return view('home');
});
Route::get('/home', function () {
    return view('home');
});
Route::get('/contact', function () {
    return view('contact');
});
Route::get('/greet/{name}', function ($name) {
    return view('greet',['name' => $name]);
});
Route::get('/user/{id}', function ($id) {
    return view('user',['id' => $id]);
});