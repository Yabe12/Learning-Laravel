

### 1. **Create a migration for a `products` table with fields like `name`, `price`, and `stock`.**

#### Migration:

Run the migration command to create the `products` table:

```bash
php artisan make:migration create_products_table
```

In the generated migration file (`database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php`):

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 8, 2); // Price with two decimal places
        $table->integer('stock');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('products');
}
```

Run the migration to create the table:

```bash
php artisan migrate
```

**Explanation:**
- The `products` table contains three fields: `name`, `price`, and `stock`, along with timestamps (`created_at`, `updated_at`).

---

### 2. **Use a factory to generate 50 dummy products and seed them into the database.**

#### Factory:

Create a product factory using:

```bash
php artisan make:factory ProductFactory --model=Product
```

In `database/factories/ProductFactory.php`:

```php
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->word(),
        'price' => $faker->randomFloat(2, 10, 100), // Price between 10 and 100
        'stock' => $faker->numberBetween(1, 100),  // Stock between 1 and 100
    ];
});
```

#### Seeding:

In `database/seeders/DatabaseSeeder.php`:

```php
public function run()
{
    \App\Models\Product::factory(50)->create();
}
```

Run the seeder:

```bash
php artisan db:seed
```

**Explanation:**
- A factory is used to generate random data for the `products` table.
- The `Product::factory(50)->create()` method generates and inserts 50 dummy products into the database.

---

### 3. **Set up a one-to-many relationship between `Category` and `Product` and fetch all products under a specific category.**

#### Migration for `categories` table:

```php
public function up()
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

#### Eloquent Models:

In `app/Models/Category.php`:

```php
public function products()
{
    return $this->hasMany(Product::class);
}
```

In `app/Models/Product.php`:

```php
public function category()
{
    return $this->belongsTo(Category::class);
}
```

#### Fetch Products for a Specific Category:

```php
$category = Category::find(1); // Get category with ID 1
$products = $category->products; // Get all products under this category
```

**Explanation:**
- The `Category` model has a `hasMany` relationship with `Product`, and the `Product` model has a `belongsTo` relationship with `Category`.
- Using `$category->products`, you can fetch all products under a specific category.

---

### 4. **Define a `User` and `Profile` one-to-one relationship and fetch the profile for a given user.**

#### Migration for `profiles` table:

```php
public function up()
{
    Schema::create('profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('bio');
        $table->timestamps();
    });
}
```

#### Eloquent Models:

In `app/Models/User.php`:

```php
public function profile()
{
    return $this->hasOne(Profile::class);
}
```

In `app/Models/Profile.php`:

```php
public function user()
{
    return $this->belongsTo(User::class);
}
```

#### Fetch the Profile for a User:

```php
$user = User::find(1);
$profile = $user->profile; // Get the user's profile
```

**Explanation:**
- The `User` model has a `hasOne` relationship with `Profile`, and the `Profile` model has a `belongsTo` relationship with `User`.
- You can fetch the profile for a given user by accessing `$user->profile`.

---

### 5. **Implement a many-to-many relationship between `User` and `Role` and attach roles to a user.**

#### Migration for `roles` table and pivot table `role_user`:

```php
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('role_user', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('role_id')->constrained()->onDelete('cascade');
        $table->primary(['user_id', 'role_id']);
    });
}
```

#### Eloquent Models:

In `app/Models/User.php`:

```php
public function roles()
{
    return $this->belongsToMany(Role::class);
}
```

In `app/Models/Role.php`:

```php
public function users()
{
    return $this->belongsToMany(User::class);
}
```

#### Attach Roles to a User:

```php
$user = User::find(1);
$user->roles()->attach(1); // Attach role with ID 1 to the user
```

**Explanation:**
- The `User` and `Role` models are connected by a many-to-many relationship using a pivot table (`role_user`).
- You can attach roles to a user using `$user->roles()->attach()`. 

---

### 6. **Use Eloquent scopes to filter products by price range.**

#### In the `Product` model:

```php
public function scopePriceRange($query, $min, $max)
{
    return $query->whereBetween('price', [$min, $max]);
}
```

#### Use the Scope:

```php
$products = Product::priceRange(10, 50)->get(); // Get products with price between 10 and 50
```

**Explanation:**
- The `scopePriceRange` method is a local scope that filters products by a price range.
- You can call the scope using `Product::priceRange(10, 50)` to get products within the given price range.

---

### 7. **Use soft deletes to archive a record and restore it later.**

#### Add the `SoftDeletes` trait to the model:

In `app/Models/Product.php`:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
}
```

#### Migration to add `deleted_at` column:

```php
public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->softDeletes();
    });
}
```

#### Soft Delete and Restore:

```php
$product = Product::find(1);
$product->delete(); // Soft delete the product

// To restore the product
$product->restore();
```

**Explanation:**
- Soft deletes are enabled by adding the `SoftDeletes` trait to the model.
- The `deleted_at` column is automatically added, and records can be "deleted" without being permanently removed from the database.

---

### 8. **Create a pivot table for orders and products and query all products in a specific order.**

#### Migration for `orders` and pivot table `order_product`:

```php
public function up()
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });

    Schema::create('order_product', function (Blueprint $table) {
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->integer('quantity');
        $table->primary(['order_id', 'product_id']);
    });
}
```

#### Eloquent Models:

In `app/Models/Order.php`:

```php
public function products()
{
    return $this->belongsToMany(Product::class)->withPivot('quantity');
}
```

#### Query All Products in an Order:

```php
$order = Order::find(1);
$products = $order->products; // Get all products in this order
```

**Explanation:**
- A many-to-many relationship is established between `orders` and `products` through the `order_product` pivot table.
- You can query all products in a specific order with `$order->products`.

---

### 9. **Perform complex queries using Eloquent Query Builder (e.g., get top 5 most expensive products).**

```php
$products = Product::orderBy('price', 'desc')->take(5)->get();
```

**Explanation:**
- This query retrieves the top 5 most expensive products by ordering them in descending order by price and limiting the result to 5.

---

### 10. **Implement a database transaction to handle multi-step data entry.**

```php
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    // Step 1: Create an order
    $order = Order::create([...]);

    // Step 2: Attach products to the order
    $order->products()->attach($productId, ['quantity' => 2]);

    // Commit the transaction
    DB::commit();
} catch

 (\Exception $e) {
    // Rollback the transaction if anything fails
    DB::rollBack();
    throw $e;
}
```

**Explanation:**
- Database transactions are used to group multiple queries into one. If any query fails, the transaction is rolled back to maintain data consistency.

---