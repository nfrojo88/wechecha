# Quick Setup Guide - Phone OTP Registration System

## 🚀 Immediate Actions Required

### Step 1: Fix Route Cache Error (URGENT)
The error you're seeing is because Laravel's routes are cached. Fix it by accessing:

```
https://www.wechechaconstruction.et/clear-route-cache.php
```

**What it does:**
- Clears route cache
- Clears config cache
- Clears application cache
- Clears view cache

**After clearing caches:**
1. The slip-sequences routes will work
2. The registration routes will be available
3. Delete the file `public/clear-route-cache.php` for security

---

### Step 2: Run Database Migrations

Access this URL from your browser:
```
https://www.wechechaconstruction.et/system/run-migrations
```

**What it does:**
- Creates `otp_verifications` table
- Adds `phone_verified` columns to `users` table
- Updates `employees` table with guarantee letter fields

---

### Step 3: Test the System

#### A. Test Registration Flow

1. **Add Test Employee (as HR)**
   - Go to: https://www.wechechaconstruction.et/employees/create
   - Add employee with phone number: `+251911234567`
   - Save employee

2. **Register as Employee**
   - Go to: https://www.wechechaconstruction.et/register
   - Enter phone: `+251911234567` or `0911234567`
   - Click "Send OTP"
   - Check your phone for SMS with 6-digit code
   - Enter the OTP code
   - Create your password
   - You'll be auto-logged in!

3. **Login**
   - Go to: https://www.wechechaconstruction.et/login
   - Enter email OR phone number
   - Enter password
   - Access dashboard

---

## 📁 What Was Created

### New Files
```
app/
├── Services/
│   └── SmsEthiopiaService.php          # SMS Ethiopia API integration
└── Http/Controllers/Auth/
    └── RegisterController.php           # Registration logic

app/Models/
└── OtpVerification.php                  # OTP model

database/migrations/
└── 2024_01_20_000004_create_otp_verifications_table.php

resources/views/
├── auth/
│   ├── register.blade.php              # Phone entry form
│   ├── verify-otp.blade.php            # OTP verification
│   └── create-password.blade.php       # Password creation
└── layouts/
    └── guest.blade.php                 # Guest layout

public/
└── clear-route-cache.php               # Cache clearing utility (DELETE after use)

Documentation/
├── REGISTRATION_SYSTEM_SETUP.md        # Full documentation
└── QUICK_SETUP_GUIDE.md                # This file
```

### Modified Files
```
routes/web.php                          # Added registration routes
app/Http/Controllers/Auth/LoginController.php  # Phone/email login
app/Models/Employee.php                 # Added isGuaranteeLetterExpired()
resources/views/auth/login.blade.php    # Updated to support phone/email
```

---

## 🔐 SMS Ethiopia Configuration

**Already Configured:**
- API Key: `6FSIE6UXV4S79DXA3GZDSJSBFWEEYV42`
- Sender ID: `1408`
- Base URL: `https://smsethiopia.com/api/v1`

**Location:** `app/Services/SmsEthiopiaService.php`

**Phone Format Support:**
- `+251911234567` ✅
- `0911234567` ✅
- `911234567` ✅

All formats are auto-converted to `251911234567` for SMS Ethiopia API.

---

## ✅ How It Works

### Registration Flow
```
1. Employee enters phone → System checks if phone exists in employees table
   ├─ Not found → Error: "Contact HR"
   └─ Found → Generate 6-digit OTP

2. Send OTP via SMS Ethiopia API → Employee receives SMS

3. Employee enters OTP → System verifies
   ├─ Invalid/Expired → Error (3 attempts max)
   └─ Valid → Mark as verified

4. Employee creates password → System creates user account

5. Auto-login → Redirect to dashboard
```

### Login Flow
```
1. User enters email OR phone + password

2. System detects input type
   ├─ Email → Login with email
   └─ Phone → Lookup employee → Find user → Login

3. Check guarantee letter status
   ├─ Expired (30+ days) → Block login
   └─ Valid → Allow login

4. Redirect to role-based dashboard
```

---

## 🎯 Key Features

### Registration
- ✅ Phone-based registration (no email needed)
- ✅ 6-digit OTP verification
- ✅ 10-minute OTP expiry
- ✅ 3 attempts limit
- ✅ Resend OTP (60-second cooldown)
- ✅ Password strength indicator
- ✅ Auto role assignment based on department

### Login
- ✅ Login with email OR phone
- ✅ Automatic guarantee letter enforcement
- ✅ Account blocking after 30 days without guarantee letter
- ✅ Role-based dashboard redirect

### Security
- ✅ OTP one-time use
- ✅ Automatic OTP cleanup
- ✅ Password hashing (bcrypt)
- ✅ Phone verification required
- ✅ Session management

---

## 🔧 Troubleshooting

### Problem: Route [slip-sequences.create] not defined
**Solution:** Access `https://www.wechechaconstruction.et/clear-route-cache.php`

### Problem: Registration route not found
**Solution:** Same as above - clear route cache

### Problem: SMS not received
**Checklist:**
- [ ] Check SMS Ethiopia account balance
- [ ] Verify phone number format
- [ ] Check `storage/logs/laravel.log` for errors
- [ ] Test with different phone number

### Problem: OTP expired
**Solution:** Click "Resend OTP" button (wait 60 seconds between requests)

### Problem: Login blocked
**Reason:** Guarantee letter not submitted within 30 days
**Solution:** Contact HR to upload guarantee letter

---

## 📊 Database Tables

### otp_verifications
Stores OTP codes for verification
```
- phone (indexed)
- otp
- verified (boolean)
- expires_at (timestamp)
- attempts (integer)
```

### users (new columns)
```
- phone_verified (boolean)
- phone_verified_at (timestamp)
```

---

## 🧪 Testing Steps

1. **Clear Caches**
   - Access: `https://www.wechechaconstruction.et/clear-route-cache.php`
   - Verify success message
   - DELETE the file after use

2. **Run Migrations**
   - Access: `https://www.wechechaconstruction.et/system/run-migrations`
   - Verify success message

3. **Test Registration**
   - Create employee with phone
   - Register with that phone
   - Verify OTP received
   - Complete registration
   - Check auto-login works

4. **Test Login**
   - Logout
   - Login with email
   - Logout
   - Login with phone

5. **Test Guarantee Letter**
   - Create employee (set date_of_joining to 31 days ago)
   - Try to login
   - Should be blocked
   - Upload guarantee letter
   - Should be able to login

---

## 📞 Support Contacts

### For Employees
- **Registration Issues:** Contact HR Department
- **Login Problems:** Contact IT Support
- **OTP Not Received:** Check phone signal, wait and resend

### For HR Staff
- **Add Phone Numbers:** Required when creating employees
- **Monitor Guarantee Letters:** Check employee list for warnings
- **Upload Documents:** Use employee profile page

### For IT/Developers
- **Log Location:** `storage/logs/laravel.log`
- **OTP Records:** `otp_verifications` table
- **SMS API:** `app/Services/SmsEthiopiaService.php`

---

## 📝 Important Notes

1. **Security:** Delete `public/clear-route-cache.php` after use
2. **Phone Format:** Accepts +251, 0, or direct format
3. **OTP Expiry:** 10 minutes from generation
4. **Guarantee Letter:** 30-day deadline from date_of_joining
5. **Role Assignment:** Automatic based on department

---

## ✨ Next Steps

After setup is complete:

1. **Train HR Staff**
   - How to add employee phone numbers
   - How to upload guarantee letters
   - How to monitor deadlines

2. **Train Employees**
   - Registration process
   - Login with email/phone
   - Password requirements
   - Guarantee letter deadline

3. **Monitor System**
   - Check SMS delivery success rate
   - Monitor OTP usage
   - Review guarantee letter compliance
   - Check user registration completion rate

---

**Quick Links:**
- 🔄 Clear Cache: https://www.wechechaconstruction.et/clear-route-cache.php
- 🔨 Run Migrations: https://www.wechechaconstruction.et/system/run-migrations
- 📝 Register: https://www.wechechaconstruction.et/register
- 🔐 Login: https://www.wechechaconstruction.et/login
- 👥 Slip Sequences: https://www.wechechaconstruction.et/store-manager/slip-sequences

---

**Status:** ✅ Implementation Complete  
**Version:** 1.0  
**Date:** January 2024
