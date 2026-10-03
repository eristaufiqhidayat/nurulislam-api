Untuk menginstal **JWT (JSON Web Token)** di Laravel 8.5 dan mengimplementasikannya dengan contoh data user, ikuti langkah-langkah berikut:

---

### **1. Install Laravel 8.5 (Jika Belum Ada)**
Jika belum memiliki proyek Laravel 8.5, buat dengan perintah:
```bash
composer create-project laravel/laravel="8.5.*" laravel-jwt
cd laravel-jwt
```

---

### **2. Install Package JWT (tymon/jwt-auth)**
Package `tymon/jwt-auth` adalah library populer untuk JWT di Laravel.

#### **Install via Composer:**
```bash
composer require tymon/jwt-auth
```
*(Versi `1.2.0` kompatibel dengan Laravel 8.5)*

#### **Publish Konfigurasi JWT:**
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

#### **Generate Secret Key:**
```bash
php artisan jwt:secret
```
*(Key akan disimpan di `.env` sebagai `JWT_SECRET`)*

---

### **3. Konfigurasi Model User**
Pastikan model `User` (`app/Models/User.php`) mengimplementasikan `JWTSubject`:
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // ...

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

---

### **4. Konfigurasi Auth Guard**
Update `config/auth.php` untuk menggunakan `jwt` sebagai guard:
```php
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],

'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

### **5. Buat Auth Controller**
Buat controller untuk handle login/register:
```bash
php artisan make:controller AuthController
```

Isi `app/Http/Controllers/AuthController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
```

---

### **6. Tambahkan Routes**
Edit `routes/api.php`:
```php
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);
```

---

### **7. Migrasi Database & Contoh Data User**
#### **Jalankan Migrasi:**
```bash
php artisan migrate
```

#### **Buat User Contoh (Opsional):**
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
]);
```

---

### **8. Testing dengan Postman**
#### **Register:**
- **URL**: `POST /api/register`
- **Body (JSON)**:
  ```json
  {
      "name": "John Doe",
      "email": "john@example.com",
      "password": "secret123"
  }
  ```

#### **Login:**
- **URL**: `POST /api/login`
- **Body (JSON)**:
  ```json
  {
      "email": "john@example.com",
      "password": "secret123"
  }
  ```
- **Response**:
  ```json
  {
      "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
  }
  ```

#### **Get User Data (Protected Route):**
- **URL**: `GET /api/me`
- **Header**:
  ```
  Authorization: Bearer <token>
  ```

---

### **Catatan Penting:**
1. Pastikan kolom `email` dan `password` ada di tabel `users`.
2. Gunakan HTTPS untuk keamanan token di production.
3. Tambahkan validasi request di controller (contoh: `$request->validate()`).

Jika ada error, cek:
- Apakah `JWT_SECRET` ada di `.env`.
- Apakah guard `api` sudah dikonfigurasi dengan benar.



### Buat Role 
Untuk **menggunakan custom role di Laravel API** (tanpa package seperti Spatie), berikut adalah cara lengkap mulai dari database hingga pengecekan role saat akses endpoint API.

---

## ✅ STRUKTUR YANG AKAN DIBUAT

* Tabel `roles` → menyimpan role seperti `admin`, `guru`, `siswa`
* Tabel `users` punya kolom `role_id`
* Relasi: `users.role_id -> roles.id`

---

## ✳️ LANGKAH 1: Migration

### 1.1 Buat tabel `roles`

```bash
php artisan make:migration create_roles_table
```

**Isi migration:**

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // contoh: admin, guru, siswa
    $table->timestamps();
});
```

### 1.2 Tambahkan `role_id` ke `users`

```bash
php artisan make:migration add_role_id_to_users_table
```

**Isi:**

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
});
```

Lalu migrate:

```bash
php artisan migrate
```

---

## ✳️ LANGKAH 2: Tambahkan Relasi di Model

### User.php

```php
public function role()
{
    return $this->belongsTo(Role::class);
}
```

### Role.php

```php
class Role extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
```

---

## ✳️ LANGKAH 3: Seeder Role (Opsional)

```bash
php artisan make:seeder RoleSeeder
```

**Isi file `RoleSeeder.php`:**

```php
public function run()
{
    \App\Models\Role::insert([
        ['name' => 'admin'],
        ['name' => 'guru'],
        ['name' => 'siswa'],
    ]);
}
```

```bash
php artisan db:seed --class=RoleSeeder
```

---

## ✳️ LANGKAH 4: Middleware `CheckRole`

```bash
php artisan make:middleware CheckRole
```

**Isi `app/Http/Middleware/CheckRole.php`:**

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->role || !in_array($user->role->name, $roles)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}
```

---

## ✳️ LANGKAH 5: Register Middleware

Di `app/Http/Kernel.php`, tambahkan:

```php
'role' => \App\Http\Middleware\CheckRole::class,
```

---

## ✳️ LANGKAH 6: Gunakan di Route API

Di `routes/api.php`:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:guru'])->group(function () {
    Route::get('/guru/siswa', [GuruController::class, 'dataSiswa']);
});

Route::middleware(['auth:sanctum', 'role:admin,guru'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

---

## ✳️ LANGKAH 7: Assign Role ke User (Contoh di seeder atau controller)

```php
$user = User::find(1);
$role = Role::where('name', 'admin')->first();
$user->role()->associate($role);
$user->save();
```

---

## ✳️ LANGKAH 8: Cek Role di Controller

Kalau mau cek langsung:

```php
if (auth()->user()->role->name !== 'admin') {
    return response()->json(['message' => 'Forbidden'], 403);
}
```

---

## ✅ BONUS: Tambahkan Role ke Response Login

```php
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'role' => $user->role->name,
        ]);
    }

    return response()->json(['message' => 'Unauthorized'], 401);
}
```

---

Kalau kamu ingin CRUD role + user + menu di backend admin Laravel juga, saya bisa bantu buatkan blueprint-nya. Mau dilanjut?


Semoga berhasil! 🚀