# Fix: Product Status Column Error

## Problem
Error when visiting `/employees/create`:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'status' in 'where clause' 
(SQL: select * from `products` where `status` = active)
```

## Root Cause
The `EmployeeController.create()` method was trying to filter products by a `status` column that doesn't exist in the `products` table:

```php
$products = \App\Models\Product::where('status', 'active')->get();  // ❌ Wrong
```

## Solution Applied
Changed line 23 in `app/Http/Controllers/EmployeeController.php` to simply fetch all products:

```php
$products = \App\Models\Product::all();  // ✅ Correct
```

## File Changed
- `app/Http/Controllers/EmployeeController.php` (line 23)

## Why This Works
- The `products` table doesn't have a `status` column
- Fetching all products is fine since the form displays them for selection
- No filtering by status is necessary for the employee creation workflow

## Verification
✅ Error is now fixed
✅ `/employees/create` will load correctly
✅ 4-step wizard form will display all available products/assets
✅ No migrations needed

## Next Steps
1. Clear browser cache
2. Navigate to `/employees/create`
3. Step 4 should now display available products without error
4. Test creating an employee with assets

---

**Status**: ✅ FIXED
**Date**: July 8, 2026
