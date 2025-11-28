# Laravel Sanctum Authentication Implementation Guide

## Table of Contents
1. [What is Sanctum?](#what-is-sanctum)
2. [Installation & Configuration](#installation--configuration)
3. [How Authentication Works](#how-authentication-works)
4. [Token Generation](#token-generation)
5. [Token Usage](#token-usage)
6. [Protected Routes](#protected-routes)
7. [Common Operations](#common-operations)
8. [Security Best Practices](#security-best-practices)
9. [Testing Authentication](#testing-authentication)
10. [Troubleshooting](#troubleshooting)

---

## What is Sanctum?

**Laravel Sanctum** provides a lightweight authentication system for SPAs (Single Page Applications), mobile apps, and simple token-based APIs.

### Two Types of Authentication:

#### 1. **Stateful (Cookie-based)**
- Suitable for SPAs where frontend and backend are on the same domain
- Uses Laravel sessions + cookies
- CSRF protection included
- No explicit token needed in headers

#### 2. **Stateless (Token-based)**
- Suitable for mobile apps, external APIs, or different domains
- Each request includes API token
- No session required
- Perfect for our project

Our project uses **Token-based (Stateless)** authentication.

---

## Installation & Configuration

### 1. Check Installation

Sanctum comes pre-installed with modern Laravel:

```bash
composer require laravel/sanctum  # If needed
```

### 2. Verify Database Table

```bash
php artisan migrate  # Creates personal_access_tokens table
```

The `personal_access_tokens` table stores tokens:
```
id
tokenable_id (User ID)
tokenable_type (Model type, e.g., "App\Models\User")
name (token name, e.g., "auth_token")
token (hashed token)
abilities (JSON - what token can do)
last_used_at (timestamp)
expires_at (nullable - when token expires)
created_at
updated_at
```

### 3. Add Trait to User Model

```php
// app/Models/User.php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;
}
```

**What HasApiTokens provides:**
- `createToken($name)` - Creates a new API token
- `tokens()` - Relationship to access tokens
- `currentAccessToken()` - Gets current token from request

### 4. Configure Middleware

**File: `bootstrap/app.php`**

```php
->withMiddleware(function (Middleware $middleware): void {
    // Ensure stateful requests are handled correctly
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);

    $middleware->alias([
        'auth.sanctum' => \Laravel\Sanctum\Http\Middleware\ValidateSecurityCsrfToken::class,
    ]);
})
```

**What this does:**
- `EnsureFrontendRequestsAreStateful`: Validates CSRF tokens for stateful requests
- Guards against CSRF attacks

### 5. Add Sanctum Guard

**File: `config/auth.php`**

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

**Why a separate guard?**
- Allows using different authentication mechanisms
- `auth:web` uses sessions
- `auth:sanctum` uses API tokens
- `auth` uses default (currently web)

---

## How Authentication Works

### Flow Diagram

```
1. User Registration
   ↓
2. Server creates User
   ↓
3. Server generates API token
   ↓
4. Client receives token
   ↓
5. Client stores token (localStorage, secure storage)
   ↓
6. Client sends token with every request in Authorization header
   ↓
7. Server validates token
   ↓
8. Server identifies user from token
   ↓
9. Request proceeds as authenticated
```

### Step-by-Step Breakdown

#### Step 1: Generate Token (Registration)

```php
// User created during registration
$user = User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => Hash::make('password123'),
]);

// Create an API token
$token = $user->createToken('auth_token')->plainTextToken;

// Token structure: {ID}|{hash}
// Example: 1|abcd1234efgh5678...
```

**What `createToken()` does:**
1. Generates a random token (64 characters)
2. Creates entry in `personal_access_tokens` table
3. Stores hashed token in database
4. Returns plain token (visible only once)
5. Client MUST store this securely

#### Step 2: Client Stores Token

```javascript
// JavaScript/React example
const response = await fetch('/api/register', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ ... })
});

const data = await response.json();
const token = data.token; // Store this!

// Store securely:
// - localStorage (simple but less secure)
// - sessionStorage (cleared on tab close)
// - Secure HttpOnly cookie (most secure)
localStorage.setItem('auth_token', token);
```

#### Step 3: Client Sends Token with Requests

```javascript
// Every subsequent request includes Authorization header
const headers = {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json'
};

fetch('/api/profile', {
  headers: headers
})
```

#### Step 4: Server Validates Token

**File: `routes/api.php`**

```php
Route::middleware(['auth:sanctum'])->group(function() {
    // All routes here require valid token
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

**What `auth:sanctum` middleware does:**

1. Checks Authorization header for Bearer token
2. Extracts token value
3. Looks up token in `personal_access_tokens` table
4. Verifies token is not expired
5. Identifies associated user
6. Sets `auth()->user()` to that user
7. If invalid → 401 Unauthorized

#### Step 5: Controller Accesses Authenticated User

```php
public function show(Request $request) {
    $user = $request->user(); // Same as auth()->user()
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);
}
```

---

## Token Generation

### Creating Tokens

```php
// Create single-use token with name
$token = $user->createToken('auth_token');

// Get plain text token (only time visible)
$plainToken = $token->plainTextToken;

// Get accessToken object
$accessToken = $token->accessToken;
$accessToken->id;      // Token ID
$accessToken->token;   // Hashed token
$accessToken->name;    // 'auth_token'
```

### Setting Token Expiration

```php
// Create token that expires in 1 hour
$token = $user->createToken('auth_token', ['*'], 
    expireIn: now()->addHour()
);

// Note: Set in personal_access_tokens.expires_at column
```

### Setting Token Abilities (Scopes)

```php
// Token with limited abilities
$token = $user->createToken('read-only', ['read']);

// Token with multiple abilities
$token = $user->createToken('full-access', ['read', 'create', 'update', 'delete']);

// Token with all abilities
$token = $user->createToken('all-access', ['*']);
```

### Revoking Tokens

```php
// Delete current token (logout)
$request->user()->currentAccessToken()->delete();

// Delete specific token
$user->tokens()->where('name', 'auth_token')->delete();

// Delete all tokens
$user->tokens()->delete();

// Check if token can perform action
if ($user->tokenCan('delete')) {
    // Can delete
}
```

---

## Token Usage

### API Requests with Token

#### cURL

```bash
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

#### JavaScript/Fetch

```javascript
const token = localStorage.getItem('auth_token');

fetch('/api/profile', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

#### Axios

```javascript
import axios from 'axios';

const token = localStorage.getItem('auth_token');

axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Now all requests automatically include the header
axios.get('/api/profile')
  .then(response => console.log(response.data));
```

#### Postman

1. Go to "Authorization" tab
2. Select "Bearer Token"
3. Paste token value
4. Headers automatically include: `Authorization: Bearer {token}`

### Response Formats

#### Success Response (200, 201)

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    "id": 1,
    "name": "John",
    "email": "john@example.com"
  },
  "token": "1|abcd1234efgh5678...",
  "token_type": "Bearer"
}
```

#### Unauthorized (401)

```json
{
  "message": "Unauthenticated."
}
```

**When this happens:**
- Token missing from header
- Token invalid/expired
- Token revoked
- Token doesn't have required ability

#### Forbidden (403)

```json
{
  "message": "This action is unauthorized."
}
```

**When this happens:**
- User authenticated but policy check fails
- User doesn't own the resource
- User lacks permission

#### Validation Error (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## Protected Routes

### Example: Profile Protection

**File: `routes/api.php`**

```php
// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require valid token)
Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});
```

### Authorization with Policies

**Controller:**

```php
public function update(UpdateProfileRequest $request, Profile $profile) {
    // First: Check authentication via middleware
    // middleware(['auth:sanctum']) passes only authenticated requests
    
    // Second: Check authorization via policy
    $this->authorize('update', $profile);
    
    // If both pass, proceed
    $profile->update($request->validated());
    
    return response()->json([...]);
}
```

**How it works:**

1. `auth:sanctum` middleware validates token
2. Sets `auth()->user()` to authenticated user
3. `$this->authorize()` calls ProfilePolicy::update()
4. Policy gets: `ProfilePolicy::update($authenticatedUser, $profile)`
5. Policy checks: `return $user->id === $profile->user_id`
6. If false → throws AuthorizationException → 403

---

## Common Operations

### 1. Register & Get Token

```php
public function register(RegisterRequest $request) {
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Create profile
    $user->profile()->create([]);

    // Create token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Registered successfully',
        'token' => $token,
        'token_type' => 'Bearer',
        'user' => new UserResource($user),
    ], 201);
}
```

### 2. Login & Get Token

```php
public function login(LoginRequest $request) {
    $user = User::where('email', $request->email)->first();

    // Validate password
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 422);
    }

    // Check email verified (security)
    if (!$user->hasVerifiedEmail()) {
        return response()->json([
            'message' => 'Please verify your email before logging in.',
        ], 403);
    }

    // Delete old tokens for cleanup
    $user->tokens()->delete();

    // Create new token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => new UserResource($user),
    ]);
}
```

### 3. Access Authenticated User

```php
public function show(Request $request) {
    // $request->user() returns authenticated User model
    $user = $request->user();
    
    // Same as:
    $user = auth('sanctum')->user();
    // Or:
    $user = auth()->user();

    return response()->json([
        'user' => new UserResource($user),
    ]);
}
```

### 4. Logout (Revoke Token)

```php
public function logout(Request $request) {
    // Delete current token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ]);
}
```

### 5. Check Token Abilities

```php
public function deleteUser(Request $request, User $user) {
    $request->user()->tokenCan('delete') 
        ? $user->delete()
        : response()->json(['message' => 'Not authorized'], 403);
}
```

---

## Security Best Practices

### 1. Token Storage

```javascript
// ❌ INSECURE: Vulnerable to XSS
localStorage.setItem('token', token);

// ✓ BETTER: HttpOnly cookie (immune to XSS)
// Set by server: Set-Cookie: auth_token=xxx; HttpOnly; Secure; SameSite=Strict

// ✓ BEST: For SPAs, use session-based auth or token with short expiration
```

### 2. Token Transmission

```javascript
// ✓ CORRECT: Bearer token in Authorization header
headers: {
  'Authorization': `Bearer ${token}`
}

// ❌ WRONG: Token in URL (logs in servers, visible in history)
fetch(`/api/profile?token=${token}`)

// ❌ WRONG: Token in GET parameter (CSRF vulnerability)
```

### 3. Token Expiration

```php
// Set expiration during creation
$token = $user->createToken('auth_token', ['*'], 
    expireIn: now()->addDays(1) // Expires in 1 day
);

// Expired tokens automatically rejected by Sanctum
```

### 4. Token Scoping (Abilities)

```php
// Login creates limited token
$token = $user->createToken('auth_token', [
    'read:profile',
    'update:profile'
]);

// Prevents token from deleting user account
// If token tries to: $user->delete() with 'delete' ability not granted
if (!$tokenCan('delete')) {
    return response()->json(['message' => 'Not authorized'], 403);
}
```

### 5. HTTPS/SSL

```
// Always use HTTPS in production
// Tokens transmitted over HTTP are exposed to MITM attacks
// Laravel middleware helps but HTTPS is essential
```

### 6. Rate Limiting

```php
// Limit login attempts to prevent brute force
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

### 7. Email Verification

```php
// Require email verification before token usage
if (!$user->hasVerifiedEmail()) {
    return response()->json([
        'message' => 'Please verify your email first',
    ], 403);
}

$token = $user->createToken('auth_token')->plainTextToken;
```

### 8. Token Rotation

```php
// Periodically issue new tokens (optional)
public function refresh(Request $request) {
    $request->user()->currentAccessToken()->delete();
    
    $newToken = $request->user()->createToken('auth_token')->plainTextToken;
    
    return response()->json([
        'token' => $newToken,
    ]);
}
```

---

## Testing Authentication

### 1. Manual Testing with Postman

**Register:**
```
POST /api/register
Body (JSON):
{
  "name": "John",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

Response:
{
  "token": "1|abc123xyz..."
}
```

**Store Token:** Copy token from response

**Protected Request:**
```
GET /api/profile
Authorization: Bearer 1|abc123xyz...

Response:
{
  "id": 1,
  "name": "John",
  "email": "john@example.com",
  ...
}
```

### 2. Testing Missing Token

```
GET /api/profile
(no Authorization header)

Response: 401 Unauthorized
{
  "message": "Unauthenticated."
}
```

### 3. Testing Invalid Token

```
GET /api/profile
Authorization: Bearer invalid_token_12345

Response: 401 Unauthorized
{
  "message": "Unauthenticated."
}
```

### 4. Testing Expired Token

If you set expiration:
```php
$token = $user->createToken('auth_token', ['*'], 
    expireIn: now()->subHour() // Already expired
);
```

```
GET /api/profile
Authorization: Bearer {expired_token}

Response: 401 Unauthorized
{
  "message": "Unauthenticated."
}
```

### 5. PHP Unit Testing

```php
// tests/Feature/AuthenticationTest.php

class AuthenticationTest extends TestCase {
    public function test_can_register() {
        $response = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['token']);
    }

    public function test_can_access_protected_route_with_token() {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/profile');

        $response->assertStatus(200);
    }

    public function test_cannot_access_protected_route_without_token() {
        $response = $this->getJson('/api/profile');
        
        $response->assertStatus(401);
    }
}
```

---

## Troubleshooting

### Issue 1: "Unauthenticated" on Protected Routes

**Cause:** Token not sent or invalid

**Solution:**
1. Verify token in Authorization header
2. Check header format: `Authorization: Bearer {token}`
3. Verify token exists in `personal_access_tokens` table
4. Check token hasn't expired

```bash
# In database:
SELECT * FROM personal_access_tokens WHERE id = 1;
```

### Issue 2: Token Not Generated

**Cause:** User model missing HasApiTokens trait

**Solution:**
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens; // Add this
}
```

### Issue 3: 401 After Login

**Cause:** Token created but middleware not configured

**Solution:**
1. Check `bootstrap/app.php` has middleware configured
2. Verify routes have `middleware(['auth:sanctum'])`
3. Check `config/auth.php` has sanctum guard

### Issue 4: Session Issues with API Calls

**Cause:** Stateful middleware interfering

**Solution:**
```php
// In bootstrap/app.php, ensure middleware applied only to API
$middleware->api(prepend: [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
]);
```

### Issue 5: CORS Issues

**Cause:** Frontend on different domain, CORS not configured

**Solution:**
```bash
composer require fruitcake/laravel-cors
php artisan vendor:publish --tag=cors
```

Configure `config/cors.php`:
```php
'allowed_origins' => [
    'http://localhost:3000', // Frontend URL
],
'supports_credentials' => true,
```

### Issue 6: Token Persists After Deletion

**Cause:** Token cached, middleware checking old token

**Solution:**
1. Clear Laravel cache: `php artisan cache:clear`
2. Ensure client removes token from storage
3. Check `personal_access_tokens` is actually deleted

---

## API Endpoints Summary

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | /api/register | No | Register new user |
| POST | /api/login | No | Login & get token |
| GET | /api/email/verify/{id}/{hash} | No | Verify email |
| POST | /api/forgot-password | No | Request password reset |
| POST | /api/reset-password | No | Reset password |
| GET | /api/user | Yes | Get current user |
| PUT | /api/user/{id} | Yes | Update user |
| DELETE | /api/user/{id} | Yes | Delete user account |
| GET | /api/profile | Yes | Get user profile |
| PUT | /api/profile | Yes | Update profile |
| DELETE | /api/profile | Yes | Delete profile |
| POST | /api/logout | Yes | Logout & revoke token |

---

## Summary

**Sanctum provides:**
- ✓ Simple token generation
- ✓ Token validation
- ✓ User identification from token
- ✓ Token abilities (scoping)
- ✓ CSRF protection
- ✓ Session support

**Key Files:**
- `app/Models/User.php` - HasApiTokens trait
- `bootstrap/app.php` - Middleware configuration
- `config/auth.php` - Guard definitions
- `config/sanctum.php` - Sanctum configuration
- `routes/api.php` - Protected routes

**Flow:**
1. Register/Login → Token created
2. Client stores token
3. Client sends token with requests
4. Server validates token via middleware
5. If valid → authenticate user
6. If invalid → 401 Unauthorized

Your project is now fully protected with Sanctum token-based authentication!
