# Sanctum Authentication - Quick Reference Card

## 🎯 At a Glance

**What:** Token-based API authentication for stateless requests  
**How:** Client sends token in `Authorization: Bearer {token}` header  
**Result:** Server validates token, identifies user, processes request  

---

## 🔑 Key Concepts

| Concept | Explanation |
|---------|-------------|
| **Token** | Unique string identifying an authenticated user (2-part: ID\|Hash) |
| **Bearer Token** | Standard HTTP header format for token-based auth |
| **Stateless** | No session required; token contains all info needed |
| **HasApiTokens** | Trait adding token functionality to User model |
| **personal_access_tokens** | Database table storing all generated tokens |
| **auth:sanctum** | Middleware validating tokens on protected routes |
| **Sanctum Guard** | Configuration telling Laravel how to authenticate API requests |

---

## 🚀 Quick Start (5 Steps)

### 1️⃣ Register User
```bash
POST /api/register
Content-Type: application/json

{
  "name": "John",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### 2️⃣ Get Token from Response
```json
{
  "token": "1|abc123xyz...",
  "token_type": "Bearer"
}
```

### 3️⃣ Store Token (Client-side)
```javascript
localStorage.setItem('auth_token', '1|abc123xyz...');
```

### 4️⃣ Send Token with Requests
```bash
GET /api/profile
Authorization: Bearer 1|abc123xyz...
```

### 5️⃣ Server Validates & Processes
```php
// Middleware validates token
// Controller gets: $request->user() = authenticated User
// Response: 200 with data
```

---

## 📋 Common Operations

### ✏️ Registration
```
POST /api/register
Body: name, email, password, password_confirmation
Response: token + user data
```

### 🔐 Login
```
POST /api/login
Body: email, password
Response: token + user data
```

### 📨 Email Verification
```
GET /api/email/verify/{id}/{hash}?expires=...&signature=...
Response: confirmation
```

### 📱 Get Current User
```
GET /api/user
Header: Authorization: Bearer {token}
Response: current user data
```

### 👤 Update User
```
PUT /api/user/{id}
Header: Authorization: Bearer {token}
Body: name, email, password
Response: updated user data
```

### 🗑️ Delete User
```
DELETE /api/user/{id}
Header: Authorization: Bearer {token}
Response: confirmation
```

### 👥 Get Profile
```
GET /api/profile
Header: Authorization: Bearer {token}
Response: profile data
```

### 📝 Update Profile
```
PUT /api/profile
Header: Authorization: Bearer {token}
Body: bio, phone, avatar, gender, dob, country, city
Response: updated profile data
```

### 🚪 Logout
```
POST /api/logout
Header: Authorization: Bearer {token}
Response: confirmation + token revoked
```

### 🔑 Forgot Password
```
POST /api/forgot-password
Body: email
Response: confirmation
```

### 🔄 Reset Password
```
POST /api/reset-password
Body: token, email, password, password_confirmation
Response: confirmation
```

---

## 🛡️ Security Essentials

| Feature | What It Does |
|---------|-------------|
| **Password Hashing** | Passwords encrypted with bcrypt, never stored plain |
| **Token Hashing** | Tokens hashed in DB, plain version shown once |
| **Email Verification** | Unverified users cannot login |
| **Signed URLs** | Email links tamper-proof, time-limited |
| **Policies** | Users can only access/modify their own data |
| **CSRF Protection** | Stateful requests validated against attacks |
| **Token Revocation** | Logout deletes token immediately |
| **Transactions** | Multi-step operations atomic (all-or-nothing) |

---

## 🧠 How It Works (Simplified)

```
1. User registers/logs in
   ↓
2. Server generates token: abc123xyz (hashed in DB)
   ↓
3. Client stores token: localStorage['auth_token']
   ↓
4. Client sends: Authorization: Bearer abc123xyz
   ↓
5. Server middleware checks: Is token in DB? Is it valid? Is it expired?
   ↓
6. Server identifies user from token
   ↓
7. Server sets $request->user() = User object
   ↓
8. Controller processes request with authenticated user
   ↓
9. Response returned
```

---

## 📊 Token Structure

```
Token: "1|abc123xyz..."
        ↑ ↑
        │ └─ Hash (unique token)
        └── User ID
```

**In Database (personal_access_tokens):**
```
id: 1
tokenable_id: 1 (user ID)
tokenable_type: "App\Models\User"
name: "auth_token"
token: "$2y$12$..." (bcrypt hashed)
abilities: ["*"]
last_used_at: 2025-01-27 10:30:00
created_at: 2025-01-27 10:00:00
updated_at: 2025-01-27 10:30:00
```

---

## ⚠️ Common Mistakes to Avoid

| ❌ Wrong | ✅ Right |
|--------|---------|
| Store token in URL | Store token in Authorization header |
| Send token in GET parameters | Send token in Bearer token format |
| Store plain password | Hash password with bcrypt |
| Trust unverified emails | Require email verification |
| Allow anyone to delete any profile | Use policies to check ownership |
| No token expiration | Set reasonable expiration times |
| Token in logs | Use secure transmission (HTTPS) |
| Same token forever | Rotate tokens periodically |

---

## 🔍 Debugging Checklist

**Getting 401 Unauthorized?**
- [ ] Is Authorization header present?
- [ ] Is format correct: `Bearer {token}`?
- [ ] Is token valid and not expired?
- [ ] Check `personal_access_tokens` table

**Getting 403 Forbidden?**
- [ ] Is user authenticated (not 401)?
- [ ] Does policy allow this action?
- [ ] Does user own the resource?
- [ ] Check UserPolicy/ProfilePolicy

**Getting 422 Validation Error?**
- [ ] Are required fields present?
- [ ] Is data in correct format?
- [ ] Check validation rules in FormRequest
- [ ] Review error message for specifics

**Token not working after logout?**
- [ ] Clear browser storage (localStorage)
- [ ] Check database - token should be deleted
- [ ] Verify logout called `->delete()` on token

---

## 📂 Key Files at a Glance

```
app/Models/User.php
  └─ use HasApiTokens; // Enables token generation

bootstrap/app.php
  └─ Sanctum middleware configuration

config/auth.php
  └─ sanctum guard definition

routes/api.php
  └─ middleware(['auth:sanctum']) // Protects routes

app/Http/Controllers/Auth/AuthController.php
  └─ createToken(), validate token logic

personal_access_tokens (table)
  └─ Stores all tokens

app/Policies/*.php
  └─ Ownership verification (authorization)
```

---

## 🧪 Testing Quick Commands

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"pass123","password_confirmation":"pass123"}'

# Copy token: "1|abc123xyz..."

# Access protected route
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer 1|abc123xyz..."

# Try without token (should fail with 401)
curl -X GET http://localhost:8000/api/profile

# Logout
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer 1|abc123xyz..."

# Try token after logout (should fail with 401)
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer 1|abc123xyz..."
```

---

## 📈 Authentication Flow Chart

```
┌─────────────────────────────────────────────────────┐
│              User Registration/Login                 │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
        ┌────────────────────┐
        │ Create User/Verify │
        │    Credentials     │
        └────────┬───────────┘
                 │
                 ▼
        ┌────────────────────┐
        │ Generate Token     │
        │ (User.createToken)│
        └────────┬───────────┘
                 │
        ┌────────▼──────────┐
        │ Token Hashing:    │
        │ Plain sent        │
        │ Hash stored in DB │
        └────────┬──────────┘
                 │
                 ▼
    ┌───────────────────────────┐
    │ Client receives token     │
    │ Stores: localStorage      │
    └────────┬──────────────────┘
             │
             ▼
    ┌───────────────────────────────────┐
    │ Request with Token:               │
    │ Authorization: Bearer {token}     │
    └────────┬────────────────────────┘
             │
             ▼
    ┌─────────────────────────────┐
    │ Sanctum Middleware Validates│
    │ 1. Extract token from header│
    │ 2. Check in personal_access │
    │ 3. Verify not expired       │
    │ 4. Identify user            │
    └────────┬────────────────────┘
             │
        ┌────┴────┐
        │          │
    Valid     Invalid
        │          │
        ▼          ▼
    Continue   401 Error
    Request    Unauthorized
        │
        ▼
    Controller Processes
    Request
        │
        ▼
    Return Response
```

---

## 💾 Database Tables Involved

| Table | Purpose |
|-------|---------|
| `users` | User accounts |
| `profiles` | User profile data |
| `personal_access_tokens` | API tokens |
| `password_resets` | Password reset tokens |
| `email_verifications` | Email verification tokens (optional) |

---

## 🔗 Default Guard Flow

```
auth() or $request->user()
  ├─ Check 'sanctum' guard (if auth:sanctum middleware)
  │  └─ Return user from token
  └─ Check 'web' guard (if auth:web middleware)
     └─ Return user from session
```

---

## 📱 Frontend Integration Example

```javascript
// Store token after registration/login
const response = await fetch('/api/register', { ... });
const data = await response.json();
localStorage.setItem('auth_token', data.token);

// Add token to all API requests
const headers = {
  'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
  'Content-Type': 'application/json'
};

// Get user profile
const profileResponse = await fetch('/api/profile', {
  method: 'GET',
  headers: headers
});

const profile = await profileResponse.json();
console.log(profile);
```

---

## 🎓 Key Takeaways

1. **Tokens identify users** - Instead of sessions
2. **Stateless** - No server-side session storage needed
3. **Secure** - Tokens hashed, transmitted via HTTPS
4. **Flexible** - Works for SPAs, mobile apps, external APIs
5. **Simple** - `auth:sanctum` middleware handles validation
6. **Authorization** - Policies enforce ownership/permissions
7. **Revocable** - Tokens can be deleted (logout)
8. **Scalable** - No session synchronization needed

---

**Your Sanctum authentication is ready! Start building your frontend! 🚀**
