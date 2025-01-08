

### 1. **Set up Laravel Breeze for user authentication (login, registration, reset password).**

#### Installation:

```bash
composer require laravel/breeze --dev
php artisan breeze:install
npm install && npm run dev
php artisan migrate
```

**Explanation:**
- `breeze:install` sets up authentication scaffolding (login, registration, password reset, etc.).
- `npm install && npm run dev` compiles the frontend assets (e.g., Tailwind CSS, Vue, etc.).
- `php artisan migrate` runs the migrations to create the necessary tables for users.

Now, you have authentication views and routes like `/login`, `/register`, and `/password/reset` ready for use.

---

### 2. **Protect specific routes using middleware like `auth` and `guest`.**

#### Using `auth` Middleware:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

#### Using `guest` Middleware:

```php
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest');
```

**Explanation:**
- The `auth` middleware ensures that only authenticated users can access the route (e.g., `/dashboard`).
- The `guest` middleware restricts access to guests (unauthenticated users), such as for the `register` route.

---

### 3. **Allow only admins to access a route using middleware.**

#### Middleware for Admin Access:

```php
Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('can:access-admin');
```

#### Define the Admin Gate:

In `AuthServiceProvider`:

```php
public function boot()
{
    $this->registerPolicies();

    Gate::define('access-admin', function ($user) {
        return $user->role === 'admin';
    });
}
```

**Explanation:**
- You can create a custom gate using `Gate::define()` to check if the user is an admin. This allows only users with a specific role (e.g., 'admin') to access the route.

---

### 4. **Use policies to define permissions for accessing and modifying a resource (e.g., posts).**

#### Create a Policy:

```bash
php artisan make:policy PostPolicy
```

In `PostPolicy`:

```php
public function update(User $user, Post $post)
{
    return $user->id === $post->user_id;
}
```

#### Register the Policy:

In `AuthServiceProvider`:

```php
protected $policies = [
    Post::class => PostPolicy::class,
];
```

#### Use Policy in Controller:

```php
public function update(Post $post)
{
    $this->authorize('update', $post);

    // Proceed to update the post
}
```

**Explanation:**
- A policy is created to define permissions for resources like posts.
- The `authorize` method checks if the user can update the post, based on the policy rule defined.

---

### 5. **Use gates to authorize specific actions in a controller.**

#### Defining Gates:

In `AuthServiceProvider`:

```php
Gate::define('delete-post', function ($user, $post) {
    return $user->id === $post->user_id;
});
```

#### Using Gates in Controller:

```php
public function delete(Post $post)
{
    if (Gate::allows('delete-post', $post)) {
        $post->delete();
        return redirect()->route('posts.index');
    }

    return response('Unauthorized', 403);
}
```

**Explanation:**
- Gates are used to authorize actions, such as checking if a user has permission to delete a specific post. You can check this via `Gate::allows()`.

---

### 6. **Implement role-based access control (RBAC) for users and roles.**

#### Define Roles in User Model:

Add a `role` column in the `users` table for RBAC:

```bash
php artisan make:migration add_role_to_users_table --table=users
```

In the migration file:

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('user');
    });
}
```

Then, in the `User` model:

```php
public function isAdmin()
{
    return $this->role === 'admin';
}
```

#### Protect Routes Based on Role:

```php
Route::get('/admin-dashboard', function () {
    return view('admin.dashboard');
})->middleware('role:admin');
```

**Explanation:**
- RBAC allows you to check a user's role (e.g., `admin`, `user`) and restrict access to certain routes or resources based on that role.

---

### 7. **Add social login functionality using Laravel Socialite.**

#### Installation:

```bash
composer require laravel/socialite
```

#### Configure Socialite in `.env`:

```env
SOCIALITE_FACEBOOK_CLIENT_ID=your-client-id
SOCIALITE_FACEBOOK_CLIENT_SECRET=your-client-secret
SOCIALITE_FACEBOOK_REDIRECT_URI=https://your-app.com/login/facebook/callback
```

#### Add Social Login Route and Controller:

```php
use Laravel\Socialite\Facades\Socialite;

Route::get('login/facebook', [SocialLoginController::class, 'redirectToFacebook']);
Route::get('login/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);
```

In the `SocialLoginController`:

```php
public function redirectToFacebook()
{
    return Socialite::driver('facebook')->redirect();
}

public function handleFacebookCallback()
{
    $user = Socialite::driver('facebook')->user();
    // Handle the user data and authenticate
}
```

**Explanation:**
- Laravel Socialite provides an easy way to integrate social login providers (e.g., Facebook, Google).
- You need to configure the credentials for each provider in the `.env` file and set up routes and controllers to handle the login process.

---

### 8. **Allow only verified email users to access certain parts of the application.**

#### Middleware for Verified Users:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('verified');
```

**Explanation:**
- The `verified` middleware ensures that only users with verified email addresses can access the route. Laravel automatically includes this middleware for email verification.

---

### 9. **Create a custom middleware to restrict access based on user age.**

#### Custom Middleware:

```bash
php artisan make:middleware AgeRestriction
```

In `AgeRestriction.php`:

```php
public function handle($request, Closure $next)
{
    if (auth()->check() && auth()->user()->age < 18) {
        return redirect('home');
    }

    return $next($request);
}
```

#### Register Middleware:

In `Kernel.php`:

```php
protected $routeMiddleware = [
    'age.restricted' => \App\Http\Middleware\AgeRestriction::class,
];
```

#### Use Middleware:

```php
Route::get('/restricted-content', function () {
    return view('restricted');
})->middleware('age.restricted');
```

**Explanation:**
- The custom middleware checks the user's age and restricts access based on their age.

---

### 10. **Implement two-factor authentication for enhanced security.**

#### Enable Two-Factor Authentication:

Run the following artisan command:

```bash
php artisan make:auth
```

#### In User Model:

```php
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthentication\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail, TwoFactorAuthenticatable
{
    use HasApiTokens, Notifiable, TwoFactorAuthenticatable;
}
```

#### Set Up Two-Factor Routes:

In `FortifyServiceProvider.php`:

```php
use Laravel\Fortify\Fortify;

Fortify::twoFactorAuthenticationView(function () {
    return view('auth.two-factor');
});
```

#### Handle Two-Factor in Controller:

```php
public function storeTwoFactor(Request $request)
{
    $request->validate([
        'code' => 'required|numeric',
    ]);

    if (auth()->user()->verifyTwoFactorCode($request->code)) {
        return redirect()->route('home');
    }

    return back()->withErrors(['code' => 'The provided code is invalid.']);
}
```

**Explanation:**
- Two-factor authentication can be enabled using Laravel Fortify, which is the recommended package for handling authentication.
- Once enabled, users will need to verify their identity through an additional code sent to their email or mobile device.

---