# Phone-Based OTP Registration System

## Overview
This document explains the phone-based employee registration system with SMS OTP verification using SMS Ethiopia API.

## Features
- ✅ Phone-only registration (no email required during registration)
- ✅ OTP verification via SMS Ethiopia API
- ✅ Support for login with either email OR phone number
- ✅ Automatic user account creation after OTP verification
- ✅ Role assignment based on department
- ✅ Guarantee letter tracking with 30-day login blocking

## Registration Flow

### Step 1: Phone Number Entry
- Employee enters their phone number
- System checks if phone exists in `employees` table
- If not found → Error: "Phone number not registered. Contact HR."
- If found → Generate 6-digit OTP and send via SMS

### Step 2: OTP Verification
- Employee receives SMS with 6-digit code
- OTP is valid for 10 minutes
- Maximum 3 attempts allowed
- Can resend OTP after 60 seconds

### Step 3: Create Password
- After successful OTP verification
- Employee creates their password (minimum 8 characters)
- Password strength indicator
- Confirm password validation

### Step 4: Auto-Login
- User account is created/updated automatically
- Phone number is marked as verified
- Auto-login to dashboard
- Role is assigned based on department

## SMS Ethiopia API Integration

### Configuration
- **API Key**: `6FSIE6UXV4S79DXA3GZDSJSBFWEEYV42`
- **Sender ID**: `1408`
- **Base URL**: `https://smsethiopia.com/api/v1`

### Service Location
`app/Services/SmsEthiopiaService.php`

### Phone Number Format
The service accepts multiple formats and converts to SMS Ethiopia format:
- `+251911234567` → `251911234567`
- `0911234567` → `251911234567`
- `911234567` → `251911234567`

## Login System

### Supported Login Methods
1. **Email**: `user@example.com`
2. **Phone**: `+251911234567`, `0911234567`, or `911234567`

### Login Process
1. User enters email OR phone number
2. System detects input type (email vs phone)
3. For phone: looks up employee → finds linked user
4. Authenticates with password
5. Checks guarantee letter status
6. If expired (30+ days without guarantee letter) → Block login
7. Redirects to role-specific dashboard

## Files Created

### Controllers
- `app/Http/Controllers/Auth/RegisterController.php` - Registration logic

### Services
- `app/Services/SmsEthiopiaService.php` - SMS Ethiopia API integration

### Models
- `app/Models/OtpVerification.php` - OTP storage and validation

### Migrations
- `database/migrations/2024_01_20_000004_create_otp_verifications_table.php`

### Views
- `resources/views/auth/register.blade.php` - Phone entry form
- `resources/views/auth/verify-otp.blade.php` - OTP verification form with timer
- `resources/views/auth/create-password.blade.php` - Password creation form
- `resources/views/layouts/guest.blade.php` - Guest layout for auth pages

### Routes
```php
// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm']);
Route::post('register/send-otp', [RegisterController::class, 'sendOtp']);
Route::get('register/verify-otp', [RegisterController::class, 'showVerifyOtpForm']);
Route::post('register/verify-otp', [RegisterController::class, 'verifyOtp']);
Route::get('register/create-password', [RegisterController::class, 'showCreatePasswordForm']);
Route::post('register/create-password', [RegisterController::class, 'createPassword']);
Route::post('register/resend-otp', [RegisterController::class, 'resendOtp']);
```

## Files Modified

### LoginController
- `app/Http/Controllers/Auth/LoginController.php`
  - Added support for phone OR email login
  - Added guarantee letter expiry check

### Login View
- `resources/views/auth/login.blade.php`
  - Changed "Email Address" to "Email or Phone Number"
  - Added register link

### Employee Model
- `app/Models/Employee.php`
  - Added `isGuaranteeLetterExpired()` method for login blocking

## Database Schema

### otp_verifications Table
```sql
CREATE TABLE otp_verifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    phone VARCHAR(255) NOT NULL,
    otp VARCHAR(255) NOT NULL,
    verified BOOLEAN DEFAULT FALSE,
    expires_at TIMESTAMP NOT NULL,
    attempts INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX(phone)
);
```

### users Table (New Columns)
```sql
ALTER TABLE users ADD COLUMN phone_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN phone_verified_at TIMESTAMP NULL;
```

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Clear Caches
Access: `https://www.wechechaconstruction.et/clear-route-cache.php`

Or manually:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Test Registration Flow

#### Step 1: Add Employee with Phone
```sql
-- HR adds employee with phone number
INSERT INTO employees (full_name, phone, email, department, date_of_joining) 
VALUES ('John Doe', '+251911234567', 'john@example.com', 'Engineering', NOW());
```

#### Step 2: Employee Registers
1. Visit: `https://www.wechechaconstruction.et/register`
2. Enter phone: `+251911234567` or `0911234567`
3. Click "Send OTP"
4. Check phone for SMS with 6-digit code
5. Enter OTP code
6. Create password
7. Auto-login to dashboard

#### Step 3: Login with Phone or Email
1. Visit: `https://www.wechechaconstruction.et/login`
2. Enter email OR phone number
3. Enter password
4. Access dashboard

## Security Features

### OTP Security
- 6-digit random code
- 10-minute expiry
- Maximum 3 attempts
- One-time use only
- Automatic cleanup of old OTPs

### Password Security
- Minimum 8 characters
- Password confirmation required
- Strength indicator
- Hashed with bcrypt

### Account Security
- Phone verification required
- Email auto-verified after phone verification
- Role-based access control
- Guarantee letter enforcement (30-day deadline)

## Role Assignment Logic

```php
// Default role
$role = 'employee';

// Department-based roles
if ($employee->department === 'HR') {
    $role = 'hr-manager';
} elseif ($employee->role_title && stripos($employee->role_title, 'engineer') !== false) {
    $role = 'site-engineer';
}

$user->assignRole($role);
```

## Guarantee Letter Enforcement

### Timeline
- **Day 0**: Employee joins, guarantee letter required
- **Day 20**: Warning flag appears
- **Day 30**: Account login blocked

### Login Check
```php
// In LoginController
if ($user->employee && $user->employee->isGuaranteeLetterExpired()) {
    Auth::logout();
    return back()->withErrors([
        'email' => 'Your account has been suspended. Please submit your guarantee letter to HR to regain access.',
    ]);
}
```

## Troubleshooting

### Route Not Found Error
If you see "Route [register] not defined":
1. Clear route cache: `php artisan route:clear`
2. Or use: `https://www.wechechaconstruction.et/clear-route-cache.php`

### SMS Not Received
1. Check SMS Ethiopia account balance
2. Verify API credentials in `app/Services/SmsEthiopiaService.php`
3. Check phone number format
4. Check Laravel logs: `storage/logs/laravel.log`

### OTP Expired
- OTPs expire after 10 minutes
- Click "Resend OTP" to get a new code
- Wait 60 seconds between resend requests

### Login Blocked
- Check if guarantee letter is overdue (30+ days)
- Contact HR to upload guarantee letter
- HR can upload via employee profile

## Testing Checklist

- [ ] HR creates employee with phone number
- [ ] Employee registers with correct phone
- [ ] Employee registers with incorrect phone (should fail)
- [ ] OTP SMS is received
- [ ] OTP verification succeeds
- [ ] OTP verification fails with wrong code
- [ ] OTP expires after 10 minutes
- [ ] Resend OTP works
- [ ] Password creation succeeds
- [ ] Auto-login works
- [ ] Login with email works
- [ ] Login with phone works
- [ ] Login blocked after 30 days without guarantee letter
- [ ] Login works after guarantee letter uploaded

## Support

### For Employees
- Phone not recognized → Contact HR Department
- OTP not received → Wait 60 seconds and resend
- Account blocked → Submit guarantee letter to HR

### For HR
- Add employee phone number when creating employee record
- Upload guarantee letter within 30 days to prevent login block
- Monitor guarantee letter deadlines in employee list

### For Developers
- SMS API logs: `storage/logs/laravel.log`
- OTP records: `otp_verifications` table
- User verification status: `users.phone_verified`
- Employee-user link: `employees.user_id`

## Future Enhancements

- [ ] Email OTP as backup delivery method
- [ ] SMS delivery status webhook
- [ ] Admin dashboard for OTP monitoring
- [ ] Rate limiting for OTP requests
- [ ] Multi-factor authentication
- [ ] Password reset via OTP
- [ ] Account recovery via HR verification

---

**Last Updated**: January 2024  
**Version**: 1.0  
**Status**: Production Ready
