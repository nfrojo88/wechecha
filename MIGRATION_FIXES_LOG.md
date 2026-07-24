# Migration Fixes Log

## Fixed Migration Errors

### Error Type 1: MySQL Identifier Length (64-char limit)
**Status: ✅ FIXED**

#### Affected Migrations:
1. **2026_07_09_create_manpower_forecast_tables.php**
   - Fixed: `mp_forecast_unique`
   - Fixed: `mp_forecast_proj_week`

2. **2026_07_09_create_leave_management_tables.php**
   - Fixed: `leave_balance_emp_type_year`

3. **2026_07_09_create_performance_tables.php**
   - Fixed: `comp_assess_emp_comp_unique`

4. **2026_07_09_enhance_employee_contracts.php**
   - Fixed: `contract_approval_emp_app_level`

5. **2026_07_09_enhance_payroll_system.php**
   - Fixed: `payroll_summary_year_month`

6. **2026_07_09_create_subcon_agreement_takeoff_items_table.php** ⭐ NEW
   - Fixed: `subcon_agreement_takeoff_unique` (was: `subcon_agreement_takeoff_items_agreement_id_takeoff_item_id_unique`)
   - Fixed: `subcon_agreement_idx`
   - Fixed: `takeoff_item_idx`

### Error Type 2: Table Already Exists
**Status: ✅ FIXED**

All migrations updated with idempotent checks:
- `Schema::hasTable()` before creating tables
- `Schema::hasColumn()` before adding columns
- Safe to re-run without errors

### Fix Implementation Pattern

**Before (Error):**
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->unique(['col1', 'col2', 'col3']); // Auto-generated name too long
});
```

**After (Fixed):**
```php
if (!Schema::hasTable('table_name')) {
    Schema::create('table_name', function (Blueprint $table) {
        // Explicit short name
        $table->unique(['col1', 'col2', 'col3'], 'short_explicit_name');
    });
}
```

---

## Index Naming Convention

All unique and composite indexes use explicit short names:
- Max 50 characters for safety (MySQL limit is 64)
- Format: `{table_abbreviation}_{type}` or `{abbreviation}_{column}_unique`
- Examples:
  - `mp_forecast_unique` (manpower_forecast unique)
  - `leave_balance_emp_type_year` (leave_balance unique)
  - `comp_assess_emp_comp_unique` (competency_assessment unique)
  - `contract_approval_emp_app_level` (contract_approval unique)
  - `payroll_summary_year_month` (payroll_summary unique)
  - `subcon_agreement_takeoff_unique` (subcon_agreement_takeoff_items unique)

---

## Verification Steps

1. **Run migrations:**
   ```bash
   php artisan migrate --force
   ```

2. **Check table status:**
   ```bash
   php artisan migrate:status
   ```

3. **Verify in MySQL:**
   ```sql
   -- Check constraint names don't exceed 64 chars
   SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
   WHERE TABLE_SCHEMA = 'database_name' 
   AND CONSTRAINT_NAME LIKE 'mp_%' 
   OR CONSTRAINT_NAME LIKE 'leave_%'
   OR CONSTRAINT_NAME LIKE 'comp_%'
   OR CONSTRAINT_NAME LIKE 'contract_%'
   OR CONSTRAINT_NAME LIKE 'payroll_%'
   OR CONSTRAINT_NAME LIKE 'subcon_%';
   ```

---

## Migration History

### Session 1
- Fixed identifier length in 5 HR migrations
- Added idempotent checks

### Session 2  
- Found and fixed `subcon_agreement_takeoff_items` migration
- Added explicit short index names
- Made migration idempotent

---

## Recommendations for Future Migrations

1. **Always use explicit index names** for tables with long names
2. **Keep table names concise** (max 35-40 chars)
3. **Use abbreviations** in constraint names
4. **Test migrations** locally before deployment
5. **Use `hasTable()` and `hasColumn()` checks** for idempotency
6. **Document all long names** that might cause issues

---

## MySQL Compatibility

✅ MySQL 5.7+
✅ MySQL 8.0+
✅ MariaDB 10.3+
✅ All identifier names under 64-character limit

