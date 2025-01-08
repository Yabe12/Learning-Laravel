

### 1. **Validate a form submission for creating a new product (e.g., name required, price numeric).**

#### Validation in Controller:

In the controller method handling the form submission:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
    ]);

    // Proceed to create the product
    Product::create($validated);

    return redirect()->route('products.index');
}
```

**Explanation:**
- `required` ensures the field is filled.
- `string` ensures the `name` is a string.
- `numeric` ensures the `price` is a number.

---

### 2. **Use the `old()` helper to repopulate form inputs after validation errors.**

#### In the Blade view:

```php
<form action="{{ route('products.store') }}" method="POST">
    @csrf
    <input type="text" name="name" value="{{ old('name') }}" placeholder="Product Name">
    <input type="text" name="price" value="{{ old('price') }}" placeholder="Product Price">
    <button type="submit">Create</button>
</form>
```

**Explanation:**
- The `old()` helper repopulates form fields with previously entered values, even after a failed validation, ensuring a better user experience.

---

### 3. **Collect and store uploaded files and validate the file type.**

#### File Upload Validation:

```php
public function store(Request $request)
{
    $request->validate([
        'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:1024', // Validates image file types and size
    ]);

    $path = $request->file('image')->store('images');

    // Save the path to the database or use as needed
    Product::create(['image_path' => $path]);

    return redirect()->route('products.index');
}
```

**Explanation:**
- `mimes:jpeg,png,jpg,gif` checks that the file is an image.
- `max:1024` ensures the file size does not exceed 1MB.

---

### 4. **Use the `Request` class to retrieve query parameters from a URL.**

```php
public function index(Request $request)
{
    $category = $request->query('category');
    $price = $request->query('price');

    // Use the query parameters in your query or logic
    $products = Product::where('category', $category)
                        ->where('price', '<=', $price)
                        ->get();

    return view('products.index', compact('products'));
}
```

**Explanation:**
- The `query()` method retrieves query parameters like `?category=electronics&price=100`.

---

### 5. **Create a custom validation rule to check if a product's name is unique.**

#### Custom Rule:

Run the following artisan command to create a custom rule:

```bash
php artisan make:rule UniqueProductName
```

In the `app/Rules/UniqueProductName.php` file:

```php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Product;

class UniqueProductName implements Rule
{
    public function passes($attribute, $value)
    {
        return Product::where('name', $value)->count() === 0;
    }

    public function message()
    {
        return 'The product name has already been taken.';
    }
}
```

#### Using the Rule:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', new UniqueProductName()],
        'price' => 'required|numeric',
    ]);
}
```

**Explanation:**
- The custom rule `UniqueProductName` checks if the product name already exists in the `products` table.
- If the name exists, it will return an error message.

---

### 6. **Use the `collect()` helper to work with a dataset and manipulate it.**

```php
public function index()
{
    $products = Product::all();

    $productNames = collect($products)->map(function ($product) {
        return $product->name;
    });

    return view('products.index', compact('productNames'));
}
```

**Explanation:**
- `collect($products)` converts the collection into a `Collection` object.
- `map()` allows transforming the collection to only contain the `name` of the products.

---

### 7. **Handle JSON request data for an API endpoint and validate the input.**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
    ]);

    $product = Product::create($validated);

    return response()->json($product, 201);
}
```

**Explanation:**
- The `validate()` method is used to validate the JSON request data in the API endpoint.
- The response is returned as JSON using `response()->json()`.

---

### 8. **Use `Request::merge()` to modify input data before validation.**

```php
public function store(Request $request)
{
    // Add a new field to the input before validation
    $request->merge(['stock' => 100]); // Default stock to 100

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
    ]);

    Product::create($validated);

    return redirect()->route('products.index');
}
```

**Explanation:**
- The `merge()` method adds or modifies input data before validation takes place.

---

### 9. **Validate nested arrays in a request (e.g., validate an array of product attributes).**

#### Validation for Nested Arrays:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'products.*.name' => 'required|string|max:255',
        'products.*.price' => 'required|numeric',
    ]);

    foreach ($validated['products'] as $productData) {
        Product::create($productData);
    }

    return redirect()->route('products.index');
}
```

**Explanation:**
- The `products.*.name` and `products.*.price` rules validate each element of the nested `products` array.

---

### 10. **Set up a form request class for validation and reuse it in multiple controllers.**

#### Creating a Form Request:

```bash
php artisan make:request ProductStoreRequest
```

In `app/Http/Requests/ProductStoreRequest.php`:

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ensure authorization is true to allow access
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ];
    }
}
```

#### Using the Form Request in the Controller:

```php
public function store(ProductStoreRequest $request)
{
    $validated = $request->validated();
    Product::create($validated);

    return redirect()->route('products.index');
}
```

**Explanation:**
- The `ProductStoreRequest` class encapsulates the validation logic.
- This request class can be reused in multiple controllers for consistency and reusability.

--- 
