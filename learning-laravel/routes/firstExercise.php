<?php

use Illuminate\Support\Facades\Route;
Route::get('/welcome', function(){
    return view('welcome');
});

// Route::get('/user/{name}', function ($id) {
//     return view('user',['name' => $name]);
// });
// Route::get('/user/{name}/{id}', function ($name, $id) {
//     return view('user', ['name' => $name, 'id' => $id]);
// });

//  Create a route that returns a simple 'Hello, Laravel!' message.
Route::get('/', function () {
    return 'Hello, Laravel!';
});
//  Define a route that accepts a parameter (e.g., user/{id}) and returns 'User ID: {id}'.
Route::get('user/{id}', function($id){
    return 'User ID: '. $id;
});
// Create a route with multiple optional parameters (e.g., profile/{name?}/{age?}).
Route::get('profile/{name?}/{age?}', function($name = null, $age = null){
    if($name && $age){
        return 'Name: '. $name . ', Age: '. $age;
    } elseif($name){
        return 'Name: '. $name;
    } elseif($age){
        return 'Age: '. $age;
    } else {
        return 'No name or age provided.';
    }
});
// Define a route that redirects from /old-route to /new-route.
Route::redirect('/old-route', '/new-route');
Route::get('/new-route',function(){
    return "Welcome to the new route!";
});
// Group routes under a common prefix, like /admin, and create routes for dashboard, users, and settings.
Route::prefix('admin')->group(function(){
    Route::get('/dashboard',function(){
        return "Welcome to the admin dashboard!";
    });
    Route::get('/users',function(){
        return "Viewing users!";
    });
    Route::get('/settings',function(){
        return "Editing settings!";
    });
});
// Use a middleware in a route group to protect a specific set of routes (e.g., /admin).

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard';
    });

    Route::get('/users', function () {
        return 'Manage Users';
    });

    Route::get('/settings', function () {
        return 'Admin Settings';
    });
});
//  Define a route that accepts a query string (e.g., /search?query=Laravel) and returns 'Search results for: {query}'.
Route::get('/search', function () {
    $query = request('query');
    return "Search results for: {$query}";
});
//  Create a route that returns JSON data for an array of products.

Route::get('/products', function(){
    $products = [
        ['id' => 1, 'name' => 'Product 1', 'price' => 10.99],
        ['id' => 2, 'name' => 'Product 2', 'price' => 15.99],
        ['id' => 3, 'name' => 'Product 3', 'price' => 20.99],
    ];
    return response()->json($products);
});
// 9. Set a route name (e.g., route('profile')) and use it in a view.

Route::get('profile',function(){
    return view('profile', ['name' => 'John Doe']);
})->name('profile');
Route::get('profile',function(){
    return view('profile');
})->name('profile');
// Create a fallback route that displays '404 - Page not found'.

Route::fallback(function(){
    return ('404');
});
Route::fallback(function () {
    return '404 - Page not found';
});