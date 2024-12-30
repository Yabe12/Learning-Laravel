<?php

use Illuminate\Support\Facades\Route;
Route::get('/', function(){
    return view('home');
});
Route::get('/user/{name}', function ($id) {
    return view('user',['name' => $name]);
});
Route::get('/user/{name}/{id}', function ($name, $id) {
    return view('user', ['name' => $name, 'id' => $id]);
});

