### Final Project: **User Management System** (Integrating Everything)

This project integrates various Laravel features for building a user management system with Blade templates for the frontend, user authentication and role-based authorization for the backend, and a database with user, roles, and permissions tables.

---

### **Step-by-Step Guide**

#### 1. **Set Up Laravel Project**

```bash
composer create-project --prefer-dist laravel/laravel user-management
cd user-management
```

#### 2. **Install Authentication and Breeze**

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

This will install and configure the default authentication routes (`login`, `register`, etc.) and front-end assets.

#### 3. **Create Database Tables for Users, Roles, and Permissions**

##### 3.1 **Migrations for Users, Roles, and Permissions**

Generate migrations for `roles`, `permissions`, and `role_user` pivot table.

```bash
php artisan make:migration create_roles_table
php artisan make:migration create_permissions_table
php artisan make:migration create_role_user_table
```

In `create_roles_table.php`:

```php
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

In `create_permissions_table.php`:

```php
public function up()
{
    Schema::create('permissions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

In `create_role_user_table.php` (pivot table):

```php
public function up()
{
    Schema::create('role_user', function (Blueprint $table) {
        $table->id();
        $table->foreignId('role_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

##### 3.2 **Seed Data for Roles and Permissions**

Generate a seeder:

```bash
php artisan make:seeder RolesPermissionsSeeder
```

In `RolesPermissionsSeeder.php`:

```php
public function run()
{
    $adminRole = Role::create(['name' => 'admin']);
    $userRole = Role::create(['name' => 'user']);
    
    $createPost = Permission::create(['name' => 'create-post']);
    $editPost = Permission::create(['name' => 'edit-post']);
    $deletePost = Permission::create(['name' => 'delete-post']);
    
    $adminRole->permissions()->attach([$createPost->id, $editPost->id, $deletePost->id]);
    $userRole->permissions()->attach([$createPost->id, $editPost->id]);
    
    User::find(1)->roles()->attach($adminRole->id); // Attach role to the first user (admin)
}
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

#### 4. **Create Role and Permission Models**

Generate models:

```bash
php artisan make:model Role
php artisan make:model Permission
```

In `Role.php`:

```php
public function permissions()
{
    return $this->belongsToMany(Permission::class);
}

public function users()
{
    return $this->belongsToMany(User::class);
}
```

In `Permission.php`:

```php
public function roles()
{
    return $this->belongsToMany(Role::class);
}
```

In `User.php` (add relationships):

```php
public function roles()
{
    return $this->belongsToMany(Role::class);
}

public function hasPermission($permission)
{
    return $this->roles()->with('permissions')->get()->contains(function ($role) use ($permission) {
        return $role->permissions->pluck('name')->contains($permission);
    });
}
```

#### 5. **Create Role-Based Authorization Logic**

Create a middleware to check permissions:

```bash
php artisan make:middleware CheckRolePermission
```

In `CheckRolePermission.php`:

```php
public function handle($request, Closure $next, $permission)
{
    if (auth()->user()->hasPermission($permission)) {
        return $next($request);
    }

    return redirect('/')->with('error', 'Unauthorized');
}
```

Register the middleware in `Kernel.php`:

```php
protected $routeMiddleware = [
    'role_permission' => \App\Http\Middleware\CheckRolePermission::class,
];
```

#### 6. **Admin Dashboard and User Management**

Create routes in `web.php` for the admin dashboard and managing users:

```php
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
    Route::resource('/admin/users', AdminUserController::class);
});
```

Create `AdminController.php` and `AdminUserController.php`.

In `AdminController.php`:

```php
public function dashboard()
{
    $users = User::all();
    return view('admin.dashboard', compact('users'));
}
```

In `AdminUserController.php` (for CRUD operations on users):

```php
public function index()
{
    $users = User::all();
    return view('admin.users.index', compact('users'));
}

public function edit(User $user)
{
    return view('admin.users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $user->update($request->all());
    return redirect()->route('admin.users.index');
}

public function destroy(User $user)
{
    $user->delete();
    return redirect()->route('admin.users.index');
}
```

#### 7. **CRUD Operations for Posts with Role-Based Permissions**

Create `Post` model and migration:

```bash
php artisan make:model Post -m
```

In `create_posts_table.php`:

```php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->timestamps();
    });
}
```

In `PostController.php`:

```php
public function store(Request $request)
{
    $this->authorize('create-post');
    Post::create($request->all());
    return redirect()->route('posts.index');
}

public function update(Request $request, Post $post)
{
    $this->authorize('edit-post');
    $post->update($request->all());
    return redirect()->route('posts.index');
}

public function destroy(Post $post)
{
    $this->authorize('delete-post');
    $post->delete();
    return redirect()->route('posts.index');
}
```

Define Post policy in `PostPolicy.php`:

```php
public function create(User $user)
{
    return $user->hasPermission('create-post');
}

public function update(User $user, Post $post)
{
    return $user->hasPermission('edit-post');
}

public function delete(User $user, Post $post)
{
    return $user->hasPermission('delete-post');
}
```

Register the policy in `AuthServiceProvider.php`:

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

#### 8. **Blade Templates for UI**

In `resources/views/layouts/app.blade.php`, set up the master layout:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @stack('styles')
</head>
<body>
    @include('partials.header')
    <div class="container">
        @yield('content')
    </div>
    @include('partials.footer')
    @stack('scripts')
</body>
</html>
```

Create a user dashboard view in `resources/views/admin/dashboard.blade.php`:

```php
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <h1>Welcome to the Admin Dashboard</h1>
    <ul>
        @foreach($users as $user)
            <li>{{ $user->name }} - {{ $user->email }}</li>
        @endforeach
    </ul>
@endsection
```

#### 9. **Final Testing**

- Ensure that roles and permissions are working as expected.
- Test user login, registration, CRUD operations for posts, and role-based access controls.
- Verify that the admin dashboard works and users can be managed accordingly.

---

### **Conclusion**

This project demonstrates how to build a comprehensive **User Management System** with:
- User authentication and role-based access control.
- CRUD operations for managing users and posts.
- Blade templates for a clean UI.
- Middleware for enforcing permissions.

