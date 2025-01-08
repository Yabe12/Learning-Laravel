

### 1. **Create a route that returns a simple 'Hello, Laravel!' message.**

```php
Route::get('/hello', function () {
    return 'Hello, Laravel!';
});
```

**Explanation:** This route responds to the `/hello` URL with a simple string message "Hello, Laravel!". The `Route::get()` method defines a route that listens for GET requests. The closure returns the string.

---

### 2. **Define a route that accepts a parameter (e.g., user/{id}) and returns 'User ID: {id}'.**

```php
Route::get('/user/{id}', function ($id) {
    return "User ID: {$id}";
});
```

**Explanation:** This route accepts a dynamic parameter `{id}`. It will capture whatever value is provided in the URL and pass it to the closure. The route then returns "User ID: {id}" where `{id}` is the dynamic parameter.

---

### 3. **Create a route with multiple optional parameters (e.g., profile/{name?}/{age?}).**

```php
Route::get('/profile/{name?}/{age?}', function ($name = 'Guest', $age = 'Unknown') {
    return "Name: {$name}, Age: {$age}";
});
```

**Explanation:** The `{name?}` and `{age?}` parameters are optional due to the `?`. If no value is passed, default values (`Guest` for name and `Unknown` for age) are used. This allows the route to accept zero, one, or both parameters.

---

### 4. **Define a route that redirects from /old-route to /new-route.**

```php
Route::redirect('/old-route', '/new-route');
```

**Explanation:** The `Route::redirect()` method is used to define a route that automatically redirects from one URL to another. In this case, visiting `/old-route` will redirect to `/new-route`.

---

### 5. **Group routes under a common prefix, like /admin, and create routes for dashboard, users, and settings.**

```php
Route::prefix('admin')->group(function () {
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
```

**Explanation:** The `Route::prefix('admin')->group()` method groups multiple routes under a common prefix `/admin`. Each route (dashboard, users, settings) now has its own unique path under `/admin`.

---

### 6. **Use a middleware in a route group to protect a specific set of routes (e.g., /admin).**

```php
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
```

**Explanation:** The `middleware('auth')` ensures that only authenticated users can access the routes within the `/admin` group. If the user is not authenticated, they will be redirected to the login page.

---

### 7. **Define a route that accepts a query string (e.g., /search?query=Laravel) and returns 'Search results for: {query}'.**

```php
Route::get('/search', function () {
    $query = request('query');
    return "Search results for: {$query}";
});
```

**Explanation:** The `request('query')` function retrieves the value of the `query` parameter from the URL's query string. For example, if the URL is `/search?query=Laravel`, it will return "Search results for: Laravel".

---

### 8. **Create a route that returns JSON data for an array of products.**

```php
Route::get('/products', function () {
    $products = [
        ['name' => 'Laptop', 'price' => 1000],
        ['name' => 'Phone', 'price' => 500],
    ];
    return response()->json($products);
});
```

**Explanation:** The `response()->json()` method is used to return JSON data. This route will return an array of products in JSON format when visited.

---

### 9. **Set a route name (e.g., route('profile')) and use it in a view.**

```php
Route::get('/profile/{id}', function ($id) {
    return "Profile of user with ID: {$id}";
})->name('profile');
```

**Explanation:** The `name()` method assigns a name to the route, which can be used for generating URLs or redirects. You can use this route name in a Blade view to generate a URL like so:

```php
<a href="{{ route('profile', ['id' => 1]) }}">Go to Profile</a>
```

---

### 10. **Create a fallback route that displays '404 - Page not found'.**

```php
Route::fallback(function () {
    return '404 - Page not found';
});
```

**Explanation:** The `Route::fallback()` method defines a fallback route that is executed when no other route matches the request. It is used to handle 404 errors, returning a custom message or view when the route is not found.

---

