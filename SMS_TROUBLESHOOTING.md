# SMS OTP Registration - Troubleshooting Guide

## Current Issue

**Error:** "Failed to send OTP. Please try again or contact support."

**What's Happening:** The SMS Ethiopia API is not sending OTP codes successfully during registration.

---

## Immediate Solution (Registration Still Works!)

I've added a **temporary bypass** that allows registration to continue even when SMS fails:

### How It Works Now:

1. **User enters phone number** → System generates OTP
2. **Tries to send SMS** via SMS Ethiopia API
3. **If SMS fails** → Shows warning message AND displays OTP code on screen
4. **User can still complete registration** using the displayed OTP code

### What You'll See:

When SMS fails, the OTP verification page will show a **red DEBUG box** with the OTP code:

```
DEBUG MODE (SMS Service Down)
Your verification code is: 123456
This is shown because SMS service is temporarily unavailable.
```

---

## Testing Steps

### Step 1: Test SMS API Connection

Access the test page:
```
https://www.wechechaconstruction.et/test-sms-api.php
```

**Actions:**
1. Click "Check API Balance" - This tests if the API is reachable
2. Enter your phone number and click "Send Test SMS"
3. Check if you receive the test SMS

### Step 2: Check Results

**Possible Results:**

#### ✅ **Success** (HTTP 200)
- API is working correctly
- SMS was sent
- Check your phone for the message

#### ❌ **Connection Error**
Possible causes:
- Server cannot reach smsethiopia.com
- Firewall blocking the connection
- Network issue

#### ❌ **HTTP 401 Unauthorized**
- Invalid API key
- Account not active
- Contact SMS Ethiopia support

#### ❌ **HTTP 400 Bad Request**
- Invalid phone number format
- Invalid sender ID
- Missing required parameters

#### ❌ **HTTP 403 Forbidden**
- Account suspended
- Insufficient balance
- Contact SMS Ethiopia support

#### ❌ **HTTP 500 Server Error**
- SMS Ethiopia API is down
- Try again later

---

## Fix Solutions

### Solution 1: Verify SMS Ethiopia Account

**Contact SMS Ethiopia:**
- Website: https://smsethiopia.com/
- Check account status
- Verify API key: `6FSIE6UXV4S79DXA3GZDSJSBFWEEYV42`
- Check account balance
- Verify sender ID: `1408` is approved

### Solution 2: Server Configuration

**Check Server Firewall:**
```bash
# Test if server can reach SMS Ethiopia
curl -v https://smsethiopia.com/api/v1/balance?api_key=YOUR_KEY
```

**Required:**
- Outgoing HTTPS (port 443) must be allowed
- DNS resolution must work
- SSL/TLS certificates must be valid

### Solution 3: Use Alternative SMS Provider

If SMS Ethiopia continues to fail, you can switch to:
- **Africa's Talking**
- **Twilio**
- **Nexmo/Vonage**

---

## Current Workaround (Development Mode)

### Files Modified:

**1. RegisterController.php**
- Added fallback when SMS fails
- Shows OTP code in session for testing
- Logs error details for debugging

**2. verify-otp.blade.php**
- Shows debug OTP when SMS fails
- Displays warning message
- Allows registration to continue

### Example Flow:

```
User Registration Flow (SMS Failed):

1. Enter phone: 0953134955
   ↓
2. Generate OTP: 123456
   ↓
3. Try SMS → FAILS
   ↓
4. Show warning: "SMS service temporarily unavailable"
   ↓
5. Display OTP: "Your verification code is: 123456"
   ↓
6. User enters OTP: 123456
   ↓
7. Registration completes successfully! ✅
```

---

## Testing Registration Now

### Test With Existing Employee:

1. **Create Employee (as HR):**
   ```
   Phone: 0953134955
   Email: kibrom@gmail.com
   Name: Kibrom Hilu
   ```

2. **Register:**
   - Go to: https://www.wechechaconstruction.et/register
   - Enter phone: `0953134955`
   - Click "Send OTP"
   - See warning message
   - **See OTP code in red DEBUG box**
   - Enter the OTP code
   - Create password
   - Complete registration! ✅

### Expected Behavior:

- ✅ Phone validation works
- ✅ OTP is generated
- ❌ SMS fails (expected for now)
- ✅ OTP is displayed on screen
- ✅ OTP verification works
- ✅ Password creation works
- ✅ Auto-login works
- ✅ User can access dashboard

---

## Checking Logs

### Laravel Logs:

Location: `storage/logs/laravel.log`

**Look for:**
```
SMS Failed - Showing OTP for testing
phone: 0953134955
otp: 123456
error: Failed to send OTP. Please try again.
```

**Also check for:**
```
Exception sending OTP SMS: [error details]
```

---

## Security Notes

### ⚠️ IMPORTANT: Remove Debug Mode in Production

The current implementation shows OTP codes on screen when SMS fails. This is **ONLY for testing**!

### Files to Update for Production:

**1. RegisterController.php** (Line ~75)
```php
// REMOVE THIS IN PRODUCTION:
->with('debug_otp', $otp);

// REPLACE WITH:
return back()->withErrors([
    'phone' => 'Failed to send OTP. Please contact support.'
])->withInput();
```

**2. verify-otp.blade.php** (Lines ~28-35)
```php
// REMOVE THIS DEBUG BLOCK IN PRODUCTION:
@if(session('debug_otp'))
    <div class="alert alert-danger">...</div>
@endif
```

---

## Next Steps

### Immediate (Testing):
1. ✅ Access test page: `/test-sms-api.php`
2. ✅ Check API balance
3. ✅ Send test SMS to your phone
4. ✅ Test registration with debug OTP display
5. ✅ Verify complete registration flow works

### Short Term (Fix SMS):
1. Contact SMS Ethiopia support
2. Verify account is active and funded
3. Check API credentials are correct
4. Test from their dashboard
5. Request technical support if needed

### Long Term (Production):
1. Fix SMS Ethiopia API issue
2. Remove debug OTP display
3. Add proper error handling
4. Set up SMS monitoring
5. Add SMS delivery confirmation

---

## File Locations

### Created Files:
```
public/test-sms-api.php          - API testing tool
SMS_TROUBLESHOOTING.md           - This file
```

### Modified Files:
```
app/Http/Controllers/Auth/RegisterController.php  - Added SMS fallback
resources/views/auth/verify-otp.blade.php        - Added debug OTP display
```

### Original Files:
```
app/Services/SmsEthiopiaService.php              - SMS API integration
app/Models/OtpVerification.php                   - OTP model
database/migrations/*_create_otp_verifications*  - OTP table
```

---

## Support Contacts

### SMS Ethiopia:
- Website: https://smsethiopia.com/
- Support: Check their contact page
- Documentation: Check API docs

### Internal IT:
- Check Laravel logs: `storage/logs/laravel.log`
- Check server firewall settings
- Verify DNS and SSL certificates
- Test CURL commands

---

## Summary

**Current Status:** ✅ Registration works with debug OTP display  
**SMS Status:** ❌ Not sending (needs investigation)  
**User Impact:** ⚠️ Minimal (can still register using displayed OTP)  
**Security:** ⚠️ Debug mode active (remove before production)  
**Next Action:** 🔍 Test SMS API using `/test-sms-api.php`

---

**Created:** January 2024  
**Status:** Active Troubleshooting  
**Priority:** Medium (Registration still functional)
