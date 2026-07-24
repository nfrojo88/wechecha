# Unified Slip Management System - Migration Guide

## ✅ Fixed & Updated

All files have been updated to work with the existing `delivery_receipts` table structure. The migration now correctly uses `dr_no` instead of `receipt_no`.

### What Changed

1. **Database Migration** (Safe & Auto-detecting)
   - File: `database/migrations/2026_07_08_update_delivery_receipts_unified_slips.php`
   - Adds columns using `Schema::hasColumn()` to avoid duplicate column errors
   - Compatible with existing table structure
   - Uses `dr_no` as the slip number field (not `receipt_no`)

2. **Controller Updates**
   - File: `app/Http/Controllers/StoreManagerController.php`
   - Updated `storeSlip()` to use `dr_no` 
   - Fixed sequence validation to use `dr_no`
   - Added auto-generation of dummy purchase orders (required by table schema)
   - Properly maps fields: `dr_no`, `received_date`, `receipt_date`, etc.

3. **View Updates**
   - File: `resources/views/store-manager/slips/index.blade.php`
   - Updated all references from `receipt_no` to `dr_no`
   - Fixed timestamp display to use `received_date`

## 🚀 How to Auto-Migrate

### Option 1: Using the Web UI (Easiest)
1. Go to: `http://yourdomain/system/run-migrations`
2. Click the button - it auto-runs migrations
3. Done! The slip system is ready

### Option 2: Using Artisan Command
```bash
php artisan migrate
```

### Option 3: Check Migration Status
```bash
php artisan migrate:status
```

## ✔️ What Gets Added to `delivery_receipts` Table

| Column | Type | Default | Purpose |
|--------|------|---------|---------|
| `slip_type` | enum | receive | Marks if slip is 'receive' or 'send' |
| `is_void` | boolean | false | Marks slips as void for audit trail |
| `sequence_status` | enum | pending | Tracks validation: valid/gap/pending |
| `to_store_id` | foreignId | null | Destination store for send slips |
| `created_by` | foreignId | null | Store manager who created slip |
| `supplier_name` | string | null | Supplier name for receive slips |
| `reference_no` | string | null | Reference number (invoice, PO, etc.) |
| `receipt_date` | date | null | Alternative date field for slips |

## 🔄 Data Integrity

- Migration checks if columns exist before adding (no errors on re-run)
- Existing data is preserved
- Old `dr_no` numbers continue to work
- New slips automatically get sequence numbers

## 📝 Slip Numbering System

**Receive Slips:** `RS-20260708-0001`
- Prefix: RS (Receive Slip)
- Date: YYYYMMDD (when created)
- Sequence: 4-digit counter per store + type

**Send Slips:** `SS-20260708-0001`
- Prefix: SS (Send Slip)
- Date: YYYYMMDD (when created)
- Sequence: 4-digit counter per store + type

## ✅ Features Now Available

1. **Unified Slip Creation** - Single form for Receive or Send
2. **Auto-Generated Numbers** - With sequence tracking
3. **Sequence Validation** - Detects gaps in slip numbering
4. **Void Tracking** - Mark invalid slips for audit
5. **Audit Trail** - See created_by and timestamps
6. **Quick Stats** - Dashboard shows totals, gaps, and voids

## 🔧 Testing After Migration

1. Go to: `/store-manager/slips/create`
2. Toggle between "Receive Slip" and "Send Slip"
3. Fill in details and items
4. Submit - slip should be created with auto-generated number
5. Go to: `/store-manager/slips` - should see the slip in records
6. Sequence validation should show "Valid"

## 📋 Error Handling

If migration still fails:
1. Check if `delivery_receipts` table exists: `php artisan tinker`
   ```
   >>> \Schema::hasTable('delivery_receipts')
   ```
2. Check existing columns: `php artisan tinker`
   ```
   >>> \Schema::getColumnListing('delivery_receipts')
   ```
3. If table doesn't exist, run all migrations: `php artisan migrate:fresh`

## 🎯 Next Steps

After migration:
1. Create test slips to verify system works
2. Test sequence validation (should see "Valid" status)
3. Mark a slip as void to test audit trail
4. Build Store Keeper dashboard (will show void slips list)
5. Implement transfer approval with slip validation

---

**Status:** ✅ Ready to migrate
**Database Support:** MySQL/MariaDB/PostgreSQL
**Backup Recommended:** Yes (before migration)
