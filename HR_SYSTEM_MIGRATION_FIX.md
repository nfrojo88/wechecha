# HR System Migration Fixes

## Issue Fixed
MySQL Identifier Length Error: `Identifier name is too long (max 64 characters)`

### Problem
Laravel's automatic index naming was creating names like `manpower_forecasts_project_id_week_starting_designation_id_unique` which exceeds MySQL's 64-character limit for identifier names.

### Solution
All migrations have been updated to use explicit, shorter index names:

## Fixed Migrations

### 1. **2026_07_09_create_manpower_forecast_tables.php**
- `mp_forecast_unique` - Replaced long auto-generated name
- `mp_forecast_proj_week` - Replaced long auto-generated name

### 2. **2026_07_09_create_leave_management_tables.php**
- `leave_balance_emp_type_year` - For `leave_balances` unique index

### 3. **2026_07_09_create_performance_tables.php**
- `comp_assess_emp_comp_unique` - For `competency_assessments` unique index

### 4. **2026_07_09_enhance_employee_contracts.php**
- `contract_approval_emp_app_level` - For `contract_approvals` unique index

### 5. **2026_07_09_enhance_payroll_system.php**
- `payroll_summary_year_month` - For `payroll_summaries` unique index

## Migration Commands

Run migrations with:
```bash
php artisan migrate --force
```

To rollback if needed:
```bash
php artisan migrate:rollback --force
```

To check migration status:
```bash
php artisan migrate:status
```

## Verification

After running migrations, verify:
1. All tables created successfully
2. All indexes with proper names (max 64 characters)
3. Foreign key constraints created
4. Unique constraints properly enforced

## Best Practices Applied

✓ Used explicit index names (max 50 characters for safety)
✓ Followed naming convention: `table_columns_type`
✓ All names are descriptive yet concise
✓ Ensures compatibility with MySQL 5.7 and 8.0+

