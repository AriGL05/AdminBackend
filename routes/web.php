<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Change at the top of your routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Always Public)
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('password/forgot', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Route::post('password/forgot', [AuthController::class, 'sendPasswordCode'])->name('password.sendPasswordCode');
Route::get('password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 2FA Authentication Routes
Route::get('/2fa', [AuthController::class, 'show2faForm'])->name('2fa.show');
Route::post('/2fa/verify', [AuthController::class, 'verify2fa'])->name('2fa.verify');
Route::get('/2fa/resend', [AuthController::class, 'resend2fa'])->name('2fa.resend');

/*
|--------------------------------------------------------------------------
| API JWT Authentication Routes
|--------------------------------------------------------------------------
*/

// API Public routes
Route::prefix('api')->group(function () {
    Route::post('login', [AuthController::class, 'apiLogin']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Protected routes with JWT
    Route::middleware(['jwt.verify'])->group(function () {
        // User info
        Route::get('user', function (Request $request) {
            return response()->json($request->user());
        });

        // Logout
        Route::post('logout', [AuthController::class, 'apiLogout']);

        // API Resources
        Route::apiResource('films', FilmController::class);
        Route::apiResource('actors', ActorController::class);
        Route::apiResource('categories', CategoryController::class);

        // Admin-only routes
        Route::middleware(['admin'])->group(function () {
            Route::apiResource('staff', StaffController::class);
        });
    });

    // Test route to verify if JWT is working
    Route::get('jwt-test', function() {
        try {
            $token = JWTAuth::parseToken();
            $user = $token->authenticate();

            if ($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'JWT is working correctly',
                    'user_id' => $user->id
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'JWT verification failed: ' . $e->getMessage()
            ], 401);
        }
    })->middleware('jwt.verify');
});

/*
|--------------------------------------------------------------------------
| Public Read-Only Routes (Accessible to Guests)
|--------------------------------------------------------------------------
*/

// Public routes that only allow reading data (GET requests)
Route::middleware(['guest.view'])->group(function () {
    // Dashboard and basic pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tablas/{tipo?}', [DashboardController::class, 'tablas'])->name('tablas');
    Route::get('/about', [DashboardController::class, 'aboutFilm'])->name('about');
    Route::get('/contact', [DashboardController::class, 'contact'])->name('contact');

    // Basic data viewing endpoints
    Route::get('/aboutfilm/{id}', [FilmController::class, 'about'])->name('aboutfilm');
    Route::get('/aboutactor/{id}', [ActorController::class, 'about'])->name('aboutactor');

    // API endpoints for basic data lists
    Route::get('/actors/all', [ApiController::class, 'getActors']);
    Route::get('/categories/all', [ApiController::class, 'getCategories']);
    Route::get('/languages/all', [ApiController::class, 'getLanguages']);
    Route::get('/api/cities', [ApiController::class, 'getCities']);

    // Basic listing endpoints
    Route::get('/actors', [ActorController::class, 'index']);
    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/address', [AddressController::class, 'index']);
    Route::get('/languages', [LanguageController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

// All routes that require authentication
Route::middleware(['auth'])->group(function () {
    // Form endpoints (POST/PUT/DELETE)
    Route::post('/contact', [DashboardController::class, 'submitContact'])->name('contact.submit');

    // Form views for creating new items
    Route::get('/newfilm', [DashboardController::class, 'newFilm'])->name('newfilm');
    Route::get('/newactor', [DashboardController::class, 'newActor'])->name('newactor');
    Route::get('/newcategory', [DashboardController::class, 'newCat'])->name('newcategory');
    Route::get('/newcustomer', [DashboardController::class, 'newCustomer'])->name('newcustomer');
    Route::get('/newaddress', [DashboardController::class, 'newAddress'])->name('newaddress');

    // Edit forms
    Route::get('/actors/{id}/edit', [ActorController::class, 'edit']);
    Route::get('/films/{id}/edit', [FilmController::class, 'edit']);
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit']);
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit']);
    Route::get('/address/{id}/edit', [AddressController::class, 'edit']);
    Route::get('/languages/{id}/edit', [LanguageController::class, 'edit']);
    Route::get('/edit/{itemType}/{itemId}', [DashboardController::class, 'editItem'])->name('edit.item');

    // Data modification routes - POST/PUT/DELETE for all resources
    Route::post('/actors', [ActorController::class, 'store']);
    Route::put('/actors/{id}', [ActorController::class, 'update']); // Add direct PUT route
    Route::put('/actors/{id}/edit', [ActorController::class, 'update']);
    Route::delete('/actors/{id}', [ActorController::class, 'destroy']);
    Route::delete('/actors/{id}/delete', [ActorController::class, 'destroy']);

    Route::post('/films', [FilmController::class, 'store']);
    Route::put('/films/{id}', [FilmController::class, 'update']); // Already fixed
    Route::put('/films/{id}/edit', [FilmController::class, 'update']);
    Route::delete('/films/{id}', [FilmController::class, 'destroy']);
    Route::delete('/films/{id}/delete', [FilmController::class, 'destroy']);

    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']); // Add direct PUT route
    Route::put('/categories/{id}/edit', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::delete('/categories/{id}/delete', [CategoryController::class, 'destroy']);

    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']); // Add direct PUT route
    Route::put('/customers/{id}/edit', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
    Route::delete('/customers/{id}/delete', [CustomerController::class, 'destroy']);

    Route::post('/address', [AddressController::class, 'store']);
    Route::put('/address/{id}', [AddressController::class, 'update']); // Add direct PUT route
    Route::put('/address/{id}/edit', [AddressController::class, 'update']);
    Route::delete('/address/{id}', [AddressController::class, 'destroy']);
    Route::delete('/address/{id}/delete', [AddressController::class, 'destroy']);

    Route::post('/languages', [LanguageController::class, 'store']);
    Route::put('/languages/{id}', [LanguageController::class, 'update']); // Add direct PUT route
    Route::put('/languages/{id}/edit', [LanguageController::class, 'update']);
    Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);

    // Staff routes (with admin protection)
    Route::get('/staff', [StaffController::class, 'index']);
    Route::post('/staff', [StaffController::class, 'store'])->middleware('admin');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->middleware('admin');
    Route::put('/staff/{id}/edit', [StaffController::class, 'update'])->middleware('admin');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->middleware('admin');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->middleware('admin');
    Route::get('/newstaff', [DashboardController::class, 'newStaff'])->middleware('admin')->name('newstaff');
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/

// Fallback route - redirect to login for guests, dashboard for authenticated users
Route::fallback(function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login')
            ->with('error', 'Please login to access this page.');
    }
});

/*
|--------------------------------------------------------------------------
| Debug Routes
|--------------------------------------------------------------------------
*/

// Add a debug route to check if users exist
Route::get('/debug/users', function () {
    if (!app()->environment('local')) {
        abort(403);
    }

    $users = \App\Models\User::all();
    return response()->json([
        'user_count' => $users->count(),
        'users' => $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at
            ];
        })
    ]);
})->middleware('auth.basic');

// Add debug route for user role checking
Route::get('/debug/check-role', function () {
    if (Auth::check()) {
        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role_id' => Auth::user()->role_id ?? 'null',
            ]
        ]);
    } else {
        return response()->json([
            'authenticated' => false,
            'message' => 'User not logged in'
        ]);
    }
});

// Add debug route for user role checking with column name
Route::get('/debug/check-columns', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $columns = [];

        // Get all column names of the user
        foreach ($user->getAttributes() as $key => $value) {
            $columns[] = $key;
        }

        return response()->json([
            'authenticated' => true,
            'user_columns' => $columns,
            'user_data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_id' => $user->rol_id ?? 'null',
                'role_id' => $user->role_id ?? 'null',
            ]
        ]);
    } else {
        return response()->json([
            'authenticated' => false,
            'message' => 'User not logged in'
        ]);
    }
});

// Debug route for staff table checking
Route::get('/debug/staff-table', function () {
    try {
        if (!Schema::hasTable('staff')) {
            return response()->json(['error' => 'Staff table does not exist']);
        }

        // Get all columns from the staff table
        $columns = DB::select('SHOW COLUMNS FROM staff');

        // Check first 5 records in the staff table
        $staffRecords = DB::table('staff')->limit(5)->get();

        // Check join tables existence
        $tablesExist = [
            'staff' => Schema::hasTable('staff'),
            'address' => Schema::hasTable('address'),
            'city' => Schema::hasTable('city'),
            'country' => Schema::hasTable('country'),
            'rol' => Schema::hasTable('rol')
        ];

        return response()->json([
            'tables_exist' => $tablesExist,
            'staff_columns' => $columns,
            'sample_data' => $staffRecords
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Debug route to check for malformed UTF-8 characters in staff table
Route::get('/debug/staff-encoding', function () {
    try {
        if (!Schema::hasTable('staff')) {
            return response()->json(['error' => 'Staff table does not exist']);
        }

        // Get raw staff data without any joins
        $staff = DB::select('SELECT * FROM staff LIMIT 10');

        // Test each field for UTF-8 validity
        $encodingIssues = [];
        foreach ($staff as $index => $member) {
            foreach ((array)$member as $field => $value) {
                if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
                    $encodingIssues[] = [
                        'staff_id' => $member->staff_id ?? $index,
                        'field' => $field,
                        'hex' => bin2hex(substr($value, 0, 30)) // Get hex representation for diagnosis
                    ];
                }
            }
        }

        // Create sanitized example
        $sanitizedExample = null;
        if (count($staff) > 0) {
            $firstMember = (array)$staff[0];
            $sanitized = [];
            foreach ($firstMember as $key => $value) {
                if (is_string($value)) {
                    $sanitized[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                } else {
                    $sanitized[$key] = $value;
                }
            }
            $sanitizedExample = $sanitized;
        }

        return response()->json([
            'has_encoding_issues' => count($encodingIssues) > 0,
            'issues' => $encodingIssues,
            'sanitization_example' => $sanitizedExample
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Add a simplified staff endpoint for debugging
Route::get('/staff-simple', function () {
    try {
        // Get staff data without complicated joins
        $staff = DB::table('staff')->select('staff_id', 'first_name', 'last_name', 'email', 'active', 'rol_id')->get();
        return response()->json($staff);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});
