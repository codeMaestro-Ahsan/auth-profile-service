# Complete Authentication System - Testing Checklist

## Pre-Test Setup
- [ ] Start Apache in XAMPP
- [ ] Start MySQL in XAMPP
- [ ] Run: `php artisan migrate` (if not already migrated)
- [ ] Run: `php artisan serve` (if using built-in server)
- [ ] Open browser: `http://localhost:8000`
- [ ] Have Mailtrap account open to monitor emails

---

## Test 1: User Registration Flow
**Goal:** Register a new user and receive verification email

### Steps:
1. [ ] Click "Register" link in navbar (or go to `/register`)
2. [ ] Fill form:
   - Name: `Test User`
   - Email: `test@example.com`
   - Password: `password123`
   - Confirm Password: `password123`
3. [ ] Click "Register" button
4. [ ] Verify redirect to login page with success message: "Account created successfully"
5. [ ] Check Mailtrap inbox - should receive verification email
6. [ ] Verify email contains "Verify Email" link with signed URL

**Expected Result:**
✅ User created in database
✅ Profile created with empty data
✅ Verification email sent
✅ Redirected to login page with success message

---

## Test 2: Email Verification Flow (Successful)
**Goal:** Click verification link and get redirected with success message

### Steps:
1. [ ] Copy the verification link from Mailtrap email
2. [ ] Click the link (or paste in browser)
3. [ ] Wait for redirect (should go to login page)
4. [ ] Verify you see success message: "Email verified successfully! You can now login."
5. [ ] Verify email modal shows: "Your email is verified" with "Skip" button
6. [ ] Close modal (click Skip or wait 10 seconds)

**Expected Result:**
✅ User email_verified_at updated in database
✅ Redirected to login page
✅ Flash success message displayed
✅ Modal popup shows with auto-hide after 10 seconds

---

## Test 3: Login After Verification
**Goal:** Login with verified account

### Steps:
1. [ ] You should still be on login page
2. [ ] Fill form:
   - Email: `test@example.com`
   - Password: `password123`
3. [ ] Click "Login" button
4. [ ] Verify redirect to `/dashboard`
5. [ ] Check navbar shows "Test User" and Logout button
6. [ ] Profile should be visible with edit button

**Expected Result:**
✅ Session created
✅ User authenticated
✅ Redirected to dashboard
✅ User info displayed in navbar

---

## Test 4: Email Verification (Failed - Invalid Link)
**Goal:** Test error handling when verification link is invalid

### Steps:
1. [ ] Logout if logged in: Click Logout in navbar
2. [ ] Go to login page
3. [ ] Manually construct bad verification URL: `/verify-email/999/invalid-hash`
4. [ ] Visit the URL in browser
5. [ ] Verify redirect to login page with error: "Email verification failed. Please try again."
6. [ ] Verify modal shows: "Verification failed" with "Resend Email" button
7. [ ] Check your email is shown in modal

**Expected Result:**
✅ Invalid link rejected
✅ Redirected to login with error message
✅ Resend modal shown with email
✅ No database changes

---

## Test 5: Email Verification (Failed - Expired)
**Goal:** Test expired verification link

### Steps:
1. [ ] Generate a new user registration (repeat Test 1 with different email)
2. [ ] Wait 24+ hours OR manually set the link creation time to past
3. [ ] Try to use the old verification link
4. [ ] Verify error message and resend modal

**Expected Result:**
✅ Expired link rejected (hash validation fails)
✅ Error message displayed
✅ Resend option available

---

## Test 6: Resend Verification Email
**Goal:** Resend verification email to user who failed verification

### Steps:
1. [ ] From failed verification modal, click "Resend Email" button
2. [ ] Wait for page to refresh
3. [ ] Verify success message: "Verification email sent successfully!"
4. [ ] Check Mailtrap - should receive NEW verification email
5. [ ] Click the new verification link
6. [ ] Verify successful verification message

**Expected Result:**
✅ New verification email sent
✅ Fresh signed URL in new email
✅ Successful verification with new link
✅ User can now login

---

## Test 7: Login with Unverified Email
**Goal:** Try to login before email verification

### Steps:
1. [ ] Register new user (repeat Test 1 with new email)
2. [ ] Go to login page
3. [ ] Try to login with new unverified account
4. [ ] Fill credentials and click Login

**Expected Result:**
✅ Error message: "Please verify your email before logging in"
✅ Redirected back to login form
✅ Email field preserved

---

## Test 8: Forgot Password Flow
**Goal:** Reset password using forgot password feature

### Steps:
1. [ ] Login with verified account (Test 3)
2. [ ] Click Logout in navbar
3. [ ] Go to login page
4. [ ] Click "Forgot your password?" link
5. [ ] Should redirect to `/forgot-password`
6. [ ] Enter email: `test@example.com`
7. [ ] Click "Send Password Reset Link" button
8. [ ] Verify success message: "Password reset link sent to your email!"
9. [ ] Check Mailtrap inbox - should receive password reset email
10. [ ] Copy the reset link from email

**Expected Result:**
✅ Password reset email sent
✅ Email contains reset link with token
✅ Success message displayed
✅ Redirected back to forgot password form

---

## Test 9: Password Reset
**Goal:** Reset password using link from email

### Steps:
1. [ ] Click the password reset link from Mailtrap email
2. [ ] Should redirect to `/reset-password/{token}`
3. [ ] Page should show form with:
   - Email field (pre-filled with your email)
   - New Password field
   - Confirm Password field
4. [ ] Fill:
   - New Password: `newpassword123`
   - Confirm Password: `newpassword123`
5. [ ] Click "Reset Password" button
6. [ ] Verify success message: "Password has been reset successfully!"
7. [ ] Should redirect to login page

**Expected Result:**
✅ Password updated in database
✅ Success message displayed
✅ Old password no longer works
✅ Redirected to login

---

## Test 10: Login with New Password
**Goal:** Verify new password works after reset

### Steps:
1. [ ] You should be on login page after Test 9
2. [ ] Try old password:
   - Email: `test@example.com`
   - Password: `password123`
   - Click Login
3. [ ] Verify error: "Invalid credentials"
4. [ ] Try new password:
   - Email: `test@example.com`
   - Password: `newpassword123`
   - Click Login
5. [ ] Verify successful login to dashboard

**Expected Result:**
✅ Old password rejected
✅ New password accepted
✅ Login successful
✅ Dashboard displayed

---

## Test 11: Profile Management
**Goal:** Update user profile

### Steps:
1. [ ] Should be on dashboard after login (Test 10)
2. [ ] Click "Edit Profile" button
3. [ ] Fill profile form:
   - Bio: "I am a test user"
   - Company: "Test Company"
   - Location: "Test City"
4. [ ] Optional: Upload profile picture
5. [ ] Click "Update Profile" button
6. [ ] Verify success message: "Profile updated successfully"
7. [ ] Go back to dashboard - verify profile updated

**Expected Result:**
✅ Profile data saved in database
✅ Profile picture uploaded to storage
✅ Success message displayed
✅ Dashboard shows updated profile

---

## Test 12: User Discovery
**Goal:** View other users' profiles

### Steps:
1. [ ] Should be logged in
2. [ ] Click "Users" link in navbar
3. [ ] Should see list of all users
4. [ ] Click on another user's name/profile
5. [ ] Should see their profile page with their info
6. [ ] Verify read-only view (no edit options)

**Expected Result:**
✅ Users list displayed
✅ Can view other users' profiles
✅ Cannot edit others' profiles
✅ Profile data displayed correctly

---

## Test 13: Account Settings
**Goal:** Change account email/password

### Steps:
1. [ ] Click "Account" link in navbar
2. [ ] Should see current email displayed
3. [ ] Optional: Click "Update Email" to change email
4. [ ] Optional: Click "Update Password" to change password
5. [ ] Fill form and click update button
6. [ ] Verify success message

**Expected Result:**
✅ Account settings accessible
✅ Changes saved to database
✅ Success message displayed

---

## Test 14: Logout
**Goal:** Verify logout functionality

### Steps:
1. [ ] Should be logged in
2. [ ] Click "Logout" button in navbar
3. [ ] Verify redirect to home page
4. [ ] Verify navbar shows "Login" and "Register" links again
5. [ ] Try to access `/dashboard` directly
6. [ ] Should redirect to login page

**Expected Result:**
✅ Session destroyed
✅ Redirected to home
✅ Cannot access protected routes
✅ Must login again to access dashboard

---

## Test 15: Form Validation
**Goal:** Test validation on all forms

### Registration Form:
- [ ] Submit empty form - should show validation errors
- [ ] Submit with mismatched passwords - should show error
- [ ] Submit with existing email - should show error
- [ ] Submit with invalid email - should show error

### Login Form:
- [ ] Submit with non-existent email - should show error
- [ ] Submit with wrong password - should show error

### Forgot Password Form:
- [ ] Submit with non-existent email - should show error
- [ ] Submit empty form - should show error

### Reset Password Form:
- [ ] Submit with mismatched passwords - should show error
- [ ] Submit with weak password - should show error

**Expected Result:**
✅ All validation errors displayed
✅ Helpful error messages shown
✅ Invalid data rejected
✅ User can correct and resubmit

---

## Edge Cases & Security Tests

### Test 16: Session Security
- [ ] Open app in incognito/private window
- [ ] Login to account
- [ ] Open normal window - should NOT be logged in (separate session)
- [ ] Each browser session is independent

### Test 17: CSRF Protection
- [ ] Check all forms have `@csrf` token
- [ ] Try submitting form without CSRF token from another source
- [ ] Should show "419" or CSRF error

### Test 18: Rate Limiting (Optional)
- [ ] Try to brute force login with many attempts
- [ ] Should eventually show rate limit error

### Test 19: SQL Injection Prevention
- [ ] Try to login with email: `" OR "1"="1`
- [ ] Should not bypass authentication
- [ ] Should show invalid credentials error

---

## Test 20: Cross-Device Testing
- [ ] Test on mobile browser (or mobile emulation in DevTools)
- [ ] Verify responsive design
- [ ] All buttons clickable
- [ ] Forms readable and usable
- [ ] Modals display properly

---

## Browser Console Checks
For each test, verify:
- [ ] No JavaScript errors in browser console (F12 → Console tab)
- [ ] No 404 errors for missing assets
- [ ] No security warnings
- [ ] All API calls successful (Network tab)

---

## Database Verification
After completing all tests:
- [ ] Check users table: `SELECT * FROM users;`
- [ ] Check profiles table: `SELECT * FROM profiles;`
- [ ] Verify email_verified_at timestamps
- [ ] Verify profile data saved correctly

---

## Email Service Verification
- [ ] All emails sent via Mailtrap
- [ ] Email content clear and professional
- [ ] Links work correctly
- [ ] Resend emails generate new tokens
- [ ] No duplicate emails sent

---

## Performance Checks
- [ ] Page loads complete in < 2 seconds
- [ ] No N+1 query issues
- [ ] Database queries optimized
- [ ] Images load quickly
- [ ] No memory leaks in JavaScript

---

## Final Checklist
After all tests pass:
- [ ] All features working as expected
- [ ] No errors in console
- [ ] Database clean and consistent
- [ ] Email service functioning
- [ ] Security measures in place
- [ ] Code ready for production

**Status:** ✅ Ready for Testing

---

## Quick Test Summary
Run through these core workflows:

**Workflow 1:** Register → Verify Email → Login → View Dashboard
**Workflow 2:** Forgot Password → Reset Password → Login with New Password
**Workflow 3:** Update Profile → View Other Users → Logout

If all three workflows complete successfully, the application is production-ready!
