# Testing Checklist - Phone OTP Registration System

## ✅ Pre-Testing Setup

- [ ] Access `https://www.wechechaconstruction.et/clear-route-cache.php`
- [ ] Click "Clear All Caches" button
- [ ] Verify success message appears
- [ ] Access `https://www.wechechaconstruction.et/system/run-migrations`
- [ ] Verify migrations run successfully
- [ ] DELETE `public/clear-route-cache.php` file for security

---

## 📋 Test Scenarios

### 1. Route Cache Fix Test
**URL:** https://www.wechechaconstruction.et/store-manager/slip-sequences

- [ ] Page loads successfully (no route error)
- [ ] "Configure New Sequence" button visible
- [ ] Can click "Configure New Sequence"
- [ ] Create form loads

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 2. Registration - Valid Phone Test
**URL:** https://www.wechechaconstruction.et/register

**Prerequisites:**
- Create test employee: Phone `+251911234567`, Email `test@example.com`

**Steps:**
- [ ] Registration page loads
- [ ] Page shows "Enter your phone number to register"
- [ ] Enter phone: `+251911234567`
- [ ] Click "Send OTP"
- [ ] Success message: "OTP sent to your phone number"
- [ ] Redirects to OTP verification page
- [ ] Check phone for SMS with 6-digit code
- [ ] SMS received within 30 seconds

**Expected SMS Format:**
```
Your Construct-Pro ERP verification code is: 123456. Valid for 10 minutes. Do not share this code.
```

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed  
**OTP Received:** ⬜ Yes | ⬜ No  
**OTP Code:** _____________

---

### 3. Registration - Invalid Phone Test
**URL:** https://www.wechechaconstruction.et/register

**Steps:**
- [ ] Enter phone: `+251988888888` (non-existent)
- [ ] Click "Send OTP"
- [ ] Error message: "Phone number not registered. Please contact HR"
- [ ] Does NOT send SMS
- [ ] Stays on registration page

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 4. OTP Verification - Correct Code Test
**URL:** https://www.wechechaconstruction.et/register/verify-otp

**Prerequisites:**
- Complete "Test 2" to receive OTP

**Steps:**
- [ ] OTP verification page shows phone number
- [ ] Timer shows "10:00" and counts down
- [ ] Enter correct 6-digit OTP
- [ ] Click "Verify OTP"
- [ ] Success message: "Phone number verified successfully!"
- [ ] Redirects to create password page

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 5. OTP Verification - Wrong Code Test
**URL:** https://www.wechechaconstruction.et/register/verify-otp

**Steps:**
- [ ] Enter wrong OTP: `000000`
- [ ] Click "Verify OTP"
- [ ] Error message: "Invalid OTP. Attempts remaining: 2"
- [ ] Can try again
- [ ] Enter wrong OTP again: `111111`
- [ ] Error message: "Attempts remaining: 1"
- [ ] Third wrong attempt
- [ ] Error message: "Too many failed attempts. Please request a new OTP."

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 6. OTP Resend Test
**URL:** https://www.wechechaconstruction.et/register/verify-otp

**Steps:**
- [ ] "Resend OTP" button is disabled initially
- [ ] Counter shows "(60s)"
- [ ] Counter counts down to 0
- [ ] "Resend OTP" button becomes enabled
- [ ] Click "Resend OTP"
- [ ] Confirmation dialog appears
- [ ] Confirm resend
- [ ] New SMS received
- [ ] New OTP code is different from first one
- [ ] Counter resets to "(60s)"

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed  
**New OTP Code:** _____________

---

### 7. Password Creation Test
**URL:** https://www.wechechaconstruction.et/register/create-password

**Prerequisites:**
- Complete OTP verification

**Steps:**
- [ ] Page shows "Phone verified: +251911234567"
- [ ] Enter password: `Test1234` (8 characters)
- [ ] Password strength indicator shows
- [ ] Enter password confirmation: `Test1234`
- [ ] "Passwords match" message appears (green)
- [ ] Submit button is enabled
- [ ] Click "Complete Registration"
- [ ] Success message: "Registration completed successfully!"
- [ ] Auto-login happens
- [ ] Redirects to dashboard

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 8. Password Mismatch Test
**URL:** https://www.wechechaconstruction.et/register/create-password

**Steps:**
- [ ] Enter password: `Test1234`
- [ ] Enter password confirmation: `Test5678` (different)
- [ ] Error message: "Passwords do not match" (red)
- [ ] Submit button is disabled
- [ ] Cannot submit form

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 9. Password Strength Test
**URL:** https://www.wechechaconstruction.et/register/create-password

**Test Different Password Strengths:**

**Weak:**
- [ ] Password: `abc123` → "Weak password" (red bar, 25%)

**Fair:**
- [ ] Password: `abcd1234` → "Fair password" (yellow bar, 50%)

**Good:**
- [ ] Password: `Abcd1234` → "Good password" (blue bar, 75%)

**Strong:**
- [ ] Password: `Abcd@1234` → "Strong password" (green bar, 100%)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 10. Login with Email Test
**URL:** https://www.wechechaconstruction.et/login

**Prerequisites:**
- Complete registration with test account

**Steps:**
- [ ] Logout if logged in
- [ ] Enter email: `test@example.com`
- [ ] Enter password: `Test1234`
- [ ] Click "Sign In to Dashboard"
- [ ] Successfully logs in
- [ ] Redirects to appropriate dashboard based on role

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 11. Login with Phone Test
**URL:** https://www.wechechaconstruction.et/login

**Test Different Phone Formats:**

**Format 1: International**
- [ ] Phone: `+251911234567`
- [ ] Password: `Test1234`
- [ ] Successfully logs in

**Format 2: Ethiopian Local**
- [ ] Phone: `0911234567`
- [ ] Password: `Test1234`
- [ ] Successfully logs in

**Format 3: Short Format**
- [ ] Phone: `911234567`
- [ ] Password: `Test1234`
- [ ] Successfully logs in

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 12. Login with Wrong Credentials Test
**URL:** https://www.wechechaconstruction.et/login

**Steps:**
- [ ] Enter email: `test@example.com`
- [ ] Enter wrong password: `WrongPassword`
- [ ] Error message: "The provided credentials do not match our records."
- [ ] Does NOT log in
- [ ] Stays on login page

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 13. Guarantee Letter - Warning Test (20 Days)
**Setup:**
- Create employee with `date_of_joining` = 21 days ago
- NO guarantee letter uploaded

**Steps:**
- [ ] Go to employee profile
- [ ] Warning badge appears: "Guarantee Letter Due Soon"
- [ ] Shows days remaining: "9 days remaining"
- [ ] Employee can still login
- [ ] Dashboard shows warning notification

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 14. Guarantee Letter - Blocking Test (30 Days)
**Setup:**
- Create employee with `date_of_joining` = 31 days ago
- NO guarantee letter uploaded
- Complete registration for this employee

**Steps:**
- [ ] Try to login with this account
- [ ] Login is BLOCKED
- [ ] Error message: "Your account has been suspended. Please submit your guarantee letter to HR to regain access."
- [ ] Cannot access dashboard

**Upload Guarantee Letter (as HR):**
- [ ] HR uploads guarantee letter for this employee
- [ ] Try to login again
- [ ] Login is now SUCCESSFUL
- [ ] Can access dashboard

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 15. Register Link from Login Test
**URL:** https://www.wechechaconstruction.et/login

**Steps:**
- [ ] Login page shows "New employee? Register here" link
- [ ] Click "Register here" link
- [ ] Redirects to registration page
- [ ] Registration page loads correctly

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 16. Login Link from Register Test
**URL:** https://www.wechechaconstruction.et/register

**Steps:**
- [ ] Registration page shows "Already registered? Sign in here" link
- [ ] Click "Sign in here" link
- [ ] Redirects to login page
- [ ] Login page loads correctly

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 17. Session Timeout Test
**URL:** https://www.wechechaconstruction.et/register/verify-otp

**Steps:**
- [ ] Start registration process
- [ ] Get to OTP verification page
- [ ] Wait 11 minutes (OTP expires)
- [ ] Try to verify OTP
- [ ] Error message: "OTP expired. Please request a new one."
- [ ] Can request new OTP
- [ ] New OTP works within 10 minutes

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 18. Multiple Registration Attempts Test

**Scenario:** Same employee tries to register twice

**Steps:**
- [ ] Complete full registration for test employee
- [ ] Logout
- [ ] Try to register again with same phone
- [ ] Error message: "This phone number is already registered. Please login instead."
- [ ] Does NOT send OTP
- [ ] Shows login link

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 19. Role Assignment Test

**Test Different Departments:**

**HR Department:**
- [ ] Create employee: Department = "HR"
- [ ] Complete registration
- [ ] Check user role = "hr-manager"

**Engineering with "Engineer" Title:**
- [ ] Create employee: Department = "Engineering", Role Title = "Site Engineer"
- [ ] Complete registration
- [ ] Check user role = "site-engineer"

**Other Department:**
- [ ] Create employee: Department = "Administration"
- [ ] Complete registration
- [ ] Check user role = "employee" (default)

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

### 20. OTP Cleanup Test

**Check Database:**
- [ ] Complete registration process
- [ ] Check `otp_verifications` table
- [ ] Old OTPs (10+ minutes old) are automatically deleted
- [ ] Only recent OTPs remain

**SQL Query:**
```sql
SELECT * FROM otp_verifications 
WHERE created_at < NOW() - INTERVAL 10 MINUTE;
```

**Status:** ⬜ Not Tested | ✅ Passed | ❌ Failed

---

## 🔍 Database Verification Tests

### Check OTP Record
```sql
-- After sending OTP
SELECT * FROM otp_verifications 
WHERE phone = '+251911234567' 
ORDER BY created_at DESC 
LIMIT 1;
```

**Expected:**
- [ ] Record exists
- [ ] `verified` = 0 (false)
- [ ] `expires_at` = now() + 10 minutes
- [ ] `attempts` = 0

---

### Check User Creation
```sql
-- After completing registration
SELECT u.*, e.phone, e.full_name 
FROM users u
JOIN employees e ON e.user_id = u.id
WHERE e.phone = '+251911234567';
```

**Expected:**
- [ ] User record exists
- [ ] `phone_verified` = 1 (true)
- [ ] `phone_verified_at` is set
- [ ] `email_verified_at` is set (auto-verified)
- [ ] Password is hashed (starts with $2y$)

---

## 📊 Test Summary

### Total Tests: 20

| Category | Tests | Passed | Failed | Not Tested |
|----------|-------|--------|--------|------------|
| Route/Cache | 1 | ⬜ | ⬜ | ⬜ |
| Registration | 5 | ⬜ | ⬜ | ⬜ |
| OTP | 4 | ⬜ | ⬜ | ⬜ |
| Password | 3 | ⬜ | ⬜ | ⬜ |
| Login | 4 | ⬜ | ⬜ | ⬜ |
| Guarantee Letter | 2 | ⬜ | ⬜ | ⬜ |
| Navigation | 2 | ⬜ | ⬜ | ⬜ |
| Database | 2 | ⬜ | ⬜ | ⬜ |
| **Total** | **20** | **0** | **0** | **20** |

---

## 🐛 Issues Found

### Issue #1
**Test:** _____________  
**Description:** _____________  
**Severity:** ⬜ Critical | ⬜ High | ⬜ Medium | ⬜ Low  
**Status:** ⬜ Open | ⬜ Fixed  
**Notes:** _____________

### Issue #2
**Test:** _____________  
**Description:** _____________  
**Severity:** ⬜ Critical | ⬜ High | ⬜ Medium | ⬜ Low  
**Status:** ⬜ Open | ⬜ Fixed  
**Notes:** _____________

### Issue #3
**Test:** _____________  
**Description:** _____________  
**Severity:** ⬜ Critical | ⬜ High | ⬜ Medium | ⬜ Low  
**Status:** ⬜ Open | ⬜ Fixed  
**Notes:** _____________

---

## 📝 Test Notes

**Tester Name:** _____________  
**Test Date:** _____________  
**Environment:** Production / Staging  
**Browser:** _____________  
**Device:** _____________  

**Additional Notes:**
_____________________________________________
_____________________________________________
_____________________________________________

---

## ✅ Sign-Off

- [ ] All critical tests passed
- [ ] All high priority tests passed
- [ ] Known issues documented
- [ ] System ready for production use

**Tested By:** _____________  
**Date:** _____________  
**Signature:** _____________

**Approved By:** _____________  
**Date:** _____________  
**Signature:** _____________

---

**Status:** ⬜ Testing In Progress | ⬜ Testing Complete | ⬜ Approved for Production
