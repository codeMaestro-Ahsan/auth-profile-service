# Sanctum Setup Verification Checklist

## ✅ Installation Complete

Your project has been fully configured with Laravel Sanctum authentication. Here's what's been implemented:

---

## 1. Core Components

### ✅ Database & Migrations
- [x] `personal_access_tokens` table created (via migration)
- [x] Stores all generated tokens

### ✅ User Model Configuration
- [x] `HasApiTokens` trait added
- [x] Location: `app/Models/User.php`
- [x] Provides: `createToken()`, `tokens()`, `currentAccessToken()`

### ✅ Middleware Configuration
- [x] File: `bootstrap/app.php`
- [x] `EnsureFrontendRequestsAreStateful::class` added to API middleware
- [x] CSRF protection configured

### ✅ Guard Configuration
- [x] File: `config/auth.php`
- [x] `sanctum` guard added
- [x] Allows `auth:sanctum` middleware usage

### ✅ Routes
- [x] Public routes: register, login, email verify, forgot-password, reset-password
- [x] Protected routes: all wrapped with `middleware(['auth:sanctum'])`

---

## 2. Authentication Flow

### Registration → Token Generation
```
POST /api/register
├─ User created
├─ Profile created
├─ Token generated: $user->createToken('auth_token')
└─ Token returned to client
```

### Login → Token Generation
```
POST /api/login
├─ Credentials validated
├─ Email verification checked
├─ Old tokens deleted (optional)
├─ New token generated
└─ Token returned to client
```

### Protected Routes → Token Validation
```
GET /api/profile (with Authorization: Bearer {token})
├─ Middleware validates token
├─ Token checked in personal_access_tokens table
├─ User identified from token
└─ Request proceeds as authenticated
```

---

## 3. Security Features Implemented

### ✅ Token Security
- [x] Tokens stored hashed in database
- [x] Plain token visible only at creation
- [x] Token ID|Hash format (2-part token)
- [x] Invalid tokens automatically rejected

### ✅ Email Verification
- [x] Login requires email verified
- [x] Prevents unverified user access
- [x] Returns 403 with `requires_verification` flag

### ✅ Authorization
- [x] Policies enforce user ownership
- [x] `auth:sanctum` ensures authentication
- [x] `$this->authorize()` ensures authorization

### ✅ Password Security
- [x] Passwords hashed with bcrypt
- [x] Password reset via email token
- [x] Forgotten passwords can be recovered

### ✅ Session Handling
- [x] Logout revokes token: `$user->currentAccessToken()->delete()`
- [x] Multiple tokens per user supported
- [x] Old tokens can be deleted

---

## 4. API Endpoints Configured

### Public Endpoints
| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | /api/register | Register new user |
| POST | /api/login | Login & get token |
| GET | /api/email/verify/{id}/{hash} | Verify email |
| POST | /api/forgot-password | Request password reset |
| POST | /api/reset-password | Reset password with token |

### Protected Endpoints (Require `auth:sanctum`)
| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | /api/user | Get authenticated user |
| PUT | /api/user/{id} | Update user |
| DELETE | /api/user/{id} | Delete user account |
| GET | /api/profile | Get user profile |
| PUT | /api/profile | Update profile |
| DELETE | /api/profile | Delete profile |
| POST | /api/logout | Logout & revoke token |
| POST | /api/email/verification-notification | Resend verification email |

---

## 5. How to Use

### Registration & Token
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

Response:
{
  "success": true,
  "token": "1|abc123xyz...",
  "token_type": "Bearer"
}
```

### Using Token in Requests
```bash
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json"

Response:
{
  "id": 1,
  "user_id": 1,
  "bio": "...",
  "phone": "...",
  ...
}
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

Response:
{
  "message": "Login successful",
  "token": "2|def456uvw...",
  "user": { ... }
}
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Content-Type: application/json"

Response:
{
  "message": "Logged out successfully"
}
```

---

## 6. Key Files

| File | Purpose |
|------|---------|
| `app/Models/User.php` | HasApiTokens trait, token generation |
| `bootstrap/app.php` | Middleware configuration |
| `config/auth.php` | Guard definitions |
| `config/sanctum.php` | Sanctum settings (stateful domains, expiration) |
| `routes/api.php` | API endpoint definitions |
| `app/Http/Controllers/Auth/AuthController.php` | Register, login, logout, password reset |
| `personal_access_tokens` (table) | Token storage |

---

## 7. Testing

### Test in Postman

1. **Register:**
   - POST `http://localhost:8000/api/register`
   - Body: `{ "name": "Test", "email": "test@example.com", "password": "password123", "password_confirmation": "password123" }`
   - Copy token from response

2. **Verify Email:**
   - Check email or check database for verification link
   - GET verification link in browser

3. **Login:**
   - POST `http://localhost:8000/api/login`
   - Body: `{ "email": "test@example.com", "password": "password123" }`
   - Get new token

4. **Access Protected Route:**
   - GET `http://localhost:8000/api/profile`
   - Set Authorization header: Bearer {token}
   - Should return profile data

5. **Logout:**
   - POST `http://localhost:8000/api/logout`
   - Set Authorization header: Bearer {token}
   - Token is revoked

6. **Try Protected Route After Logout:**
   - GET `http://localhost:8000/api/profile`
   - Set Authorization header: Bearer {old_token}
   - Should return 401 Unauthorized

---

## 8. Common Issues & Solutions

### Issue: Unauthenticated (401)
**Cause:** Missing or invalid token
**Solution:** 
- Verify token in Authorization header
- Check format: `Authorization: Bearer {token}`
- Verify token not expired

### Issue: Forbidden (403)
**Cause:** User authenticated but not authorized
**Solution:**
- Check policy allows operation
- Verify user owns resource
- Check token abilities

### Issue: Token not working after logout
**Cause:** Token still in client storage
**Solution:**
- Clear localStorage/sessionStorage in browser
- Remove Authorization header

### Issue: Protected routes returning 401
**Cause:** Middleware not configured
**Solution:**
- Verify `bootstrap/app.php` has middleware
- Verify routes have `middleware(['auth:sanctum'])`
- Run `php artisan route:list` to verify

---

## 9. Production Checklist

Before deploying to production:

- [ ] Use HTTPS/SSL (tokens transmitted in plain)
- [ ] Set `APP_DEBUG=false`
- [ ] Configure `SANCTUM_STATEFUL_DOMAINS` in `.env`
- [ ] Set token expiration if needed
- [ ] Implement rate limiting on login/register
- [ ] Enable CORS if frontend on different domain
- [ ] Use environment-specific token prefixes
- [ ] Monitor `personal_access_tokens` table size
- [ ] Implement token rotation strategy
- [ ] Set up email verification as requirement

---

## 10. Next Steps

1. **Test Authentication:**
   - Run through all registration → login → profile flow
   - Verify token validation works

2. **Frontend Integration:**
   - Store token in secure storage
   - Send token with all API requests
   - Handle 401 responses (redirect to login)

3. **Advanced Features:**
   - Implement token refresh mechanism
   - Add token expiration
   - Implement token scopes/abilities
   - Add rate limiting

4. **Security Hardening:**
   - Review CORS configuration
   - Implement request signing
   - Add API versioning
   - Monitor token usage

---

## Documentation

- **Full Guide:** See `SANCTUM_AUTH_GUIDE.md`
- **Project Guide:** See `TEACHING_GUIDE.txt`
- **Installation:** See `config/sanctum.php`

---

## Summary

✅ **Sanctum authentication fully implemented and configured!**

Your project now has:
- Token-based API authentication
- User registration with email verification
- Login with token generation
- Protected routes with authorization
- Logout with token revocation
- Password reset functionality
- Complete security measures

Start testing the API endpoints using Postman or curl!
