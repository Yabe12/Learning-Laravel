

### 1. **Create a master layout with `@yield` sections for content and a `@section` for the title.**

#### Master Layout:
In `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
</head>
<body>
    <header>
        <h1>My Application</h1>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
```

#### Using the Layout:
In another Blade view, e.g., `resources/views/home.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
    <p>Welcome to the home page!</p>
@endsection
```

**Explanation:**
- `@yield('title', 'Default Title')` allows dynamic titles. If no title is provided, it will default to "Default Title."
- `@yield('content')` defines a placeholder where page-specific content will be injected when extending the master layout.

---

### 2. **Create a `header.blade.php` and `footer.blade.php` and include them in your layout using `@include`.**

#### Header and Footer:

Create `resources/views/includes/header.blade.php`:

```blade
<header>
    <h1>Welcome to My Website</h1>
</header>
```

Create `resources/views/includes/footer.blade.php`:

```blade
<footer>
    <p>&copy; {{ date('Y') }} My Website. All rights reserved.</p>
</footer>
```

#### Master Layout:

Update `resources/views/layouts/app.blade.php` to include the header and footer:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
</head>
<body>
    @include('includes.header')

    <main>
        @yield('content')
    </main>

    @include('includes.footer')
</body>
</html>
```

**Explanation:**
- `@include('includes.header')` and `@include('includes.footer')` insert the content of the header and footer views in the layout.
- This helps to reuse the header and footer across multiple views.

---

### 3. **Use Blade directives like `@if`, `@foreach`, and `@isset` to display user data conditionally.**

#### Example:

```blade
@extends('layouts.app')

@section('content')
    @isset($user)
        <h2>Welcome, {{ $user->name }}!</h2>
    @else
        <p>User not logged in</p>
    @endisset

    @if($user->is_admin)
        <p>You have admin privileges.</p>
    @else
        <p>You are a regular user.</p>
    @endif

    <h3>Your Orders:</h3>
    <ul>
        @foreach($user->orders as $order)
            <li>{{ $order->product_name }} - ${{ $order->price }}</li>
        @endforeach
    </ul>
@endsection
```

**Explanation:**
- `@isset($user)` checks if `$user` is set.
- `@if($user->is_admin)` checks if the user is an admin.
- `@foreach($user->orders as $order)` loops through the user's orders and displays each order's product name and price.

---

### 4. **Implement a Blade component for a reusable card UI (e.g., `@component('components.card')`).**

#### Card Component:

Create `resources/views/components/card.blade.php`:

```blade
<div class="card">
    <h3>{{ $title }}</h3>
    <p>{{ $slot }}</p>
</div>
```

#### Usage:

In any Blade view:

```blade
@extends('layouts.app')

@section('content')
    @component('components.card', ['title' => 'Card Title'])
        This is the content of the card.
    @endcomponent
@endsection
```

**Explanation:**
- `@component('components.card')` renders the card component.
- The `title` is passed as a parameter, and the content inside the `@component` block becomes the `$slot` in the card component.

---

### 5. **Pass data to a view and loop through it to display a list of products in a table.**

#### Route:

In `web.php`:

```php
Route::get('/products', [ProductController::class, 'index']);
```

#### Controller:

In `ProductController.php`:

```php
public function index()
{
    $products = Product::all();
    return view('products.index', compact('products'));
}
```

#### Blade View:

In `resources/views/products/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>${{ $product->price }}</td>
                    <td>{{ $product->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
```

**Explanation:**
- `compact('products')` passes the `$products` variable to the view.
- The `@foreach` directive loops through the `$products` and displays each product's name, price, and description in a table.

---

### 6. **Use Blade's `@csrf` directive to protect a form from cross-site request forgery.**

#### Blade View:

```blade
<form method="POST" action="/submit">
    @csrf
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required>
    <button type="submit">Submit</button>
</form>
```

**Explanation:**
- `@csrf` generates a hidden input field with a CSRF token to protect the form from CSRF attacks.

---

### 7. **Create a Blade template for a user profile and display dynamic user data.**

#### Blade View:

```blade
@extends('layouts.app')

@section('content')
    <h2>Profile of {{ $user->name }}</h2>
    <p>Email: {{ $user->email }}</p>
    <p>Joined: {{ $user->created_at->format('d M, Y') }}</p>
@endsection
```

**Explanation:**
- The user’s name, email, and join date are displayed dynamically by passing a `$user` object to the view.

---

### 8. **Implement a navbar using Blade components and highlight the active menu using `@if`.**

#### Navbar Component:

Create `resources/views/components/navbar.blade.php`:

```blade
<nav>
    <ul>
        <li><a href="/" @if(request()->is('/')) class="active" @endif>Home</a></li>
        <li><a href="/about" @if(request()->is('about')) class="active" @endif>About</a></li>
        <li><a href="/contact" @if(request()->is('contact')) class="active" @endif>Contact</a></li>
    </ul>
</nav>
```

#### Usage:

In the master layout, include the navbar:

```blade
@include('components.navbar')
```

**Explanation:**
- The `@if(request()->is('/'))` directive checks if the current URL matches the specified route and adds the `active` class to highlight the menu item.

---

### 9. **Use `@push` and `@stack` to inject scripts and styles in specific sections of a layout.**

#### Master Layout:

```blade
<head>
    <title>@yield('title', 'My App')</title>
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
```

#### Blade View:

```blade
@extends('layouts.app')

@section('content')
    <p>Welcome to my website!</p>
@endsection

@push('styles')
    <link rel="stylesheet" href="style.css">
@endpush

@push('scripts')
    <script src="script.js"></script>
@endpush
```

**Explanation:**
- `@push('styles')` and `@push('scripts')` allow you to add content to a specific stack that is rendered in the master layout with `@stack`.
  
---

### 10. **Create a pagination component using Blade that dynamically displays page links.**

#### Controller:

In the controller, use pagination:

```php
$products = Product::paginate(10);
return view('products.index', compact('products'));
```

#### Blade View:

```blade
@extends('layouts.app')

@section('content')
    <ul>
        @foreach($products as $product)
            <li>{{ $product->name }}</li>
        @endforeach
    </ul>

    {{ $products->links() }}
@endsection
```

**Explanation:**
- `paginate(10)` paginates the products, showing 10 per page.
- `{{ $products->links() }}` generates the pagination links.

---
