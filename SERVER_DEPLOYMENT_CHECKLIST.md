# 🚀 Server Deployment Checklist

## Employee Education & Experience Module

---

## ✅ Pre-Deployment Checklist

### 1. Upload Files to Server
Upload these files to your production server:

**New Files:**
```
database/migrations/
  ✓ 2024_01_20_000001_create_employee_education_table.php
  ✓ 2024_01_20_000002_create_employee_experience_table.php

app/Models/
  ✓ EmployeeEducation.php
  ✓ EmployeeExperience.php

app/Http/Controllers/
  ✓ EmployeeController.php (updated)

resources/views/hr/employees/
  ✓ create.blade.php (updated)
  ✓ show.blade.php (updated)

Documentation/
  ✓ All *.md files
```

---

## 🔧 Server Commands

### Step 1: Run Migrations
```bash
cd /data/var/www/vhosts/wechechaconstruction.et/httpdocs
php artisan migrate
```

**Expected Output:**
```
Migrating: 2024_01_20_000001_create_employee_education_table
Migrated:  2024_01_20_000001_create_employee_education_table (45.23ms)
Migrating: 2024_01_20_000002_create_employee_experience_table
Migrated:  2024_01_20_000002_create_employee_experience_table (38.91ms)
```

**If Error:**
```bash
# Check database connection
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback
```

---

### Step 2: Create Storage Link
```bash
php artisan storage:link
```

**Expected Output:**
```
The [public/storage] link has been connected to [storage/app/public].
```

**Manual Alternative:**
```bash
ln -s /data/var/www/vhosts/wechechaconstruction.et/httpdocs/storage/app/public \
      /data/var/www/vhosts/wechechaconstruction.et/httpdocs/public/storage
```

---

### Step 3: Create Storage Directories
```bash
mkdir -p storage/app/public/employee_certificates
mkdir -p storage/app/public/employee_licenses
mkdir -p storage/app/public/temp/education
mkdir -p storage/app/public/temp/experience
```

---

### Step 4: Set Permissions
```bash
chmod -R 755 storage/app/public
chmod -R 755 storage/app/public/employee_certificates
chmod -R 755 storage/app/public/employee_licenses
chmod -R 755 storage/app/public/temp

# Set ownership if needed
chown -R www-data:www-data storage/app/public
```

---

### Step 5: Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

**Expected Output:**
```
Configuration cache cleared!
Application cache cleared!
Compiled views cleared!
Route cache cleared!
Files cached successfully!
```

---

### Step 6: Verify Installation
```bash
# Check migrations ran
php artisan migrate:status | grep employee

# Check storage link
ls -la public/storage

# Check directories exist
ls -la storage/app/public/
```

**Expected:**
```
✓ employee_education table exists
✓ employee_experience table exists
✓ public/storage symlink exists
✓ employee_certificates/ directory exists
✓ employee_licenses/ directory exists
```

---

## 🌐 Web-Based Verification

### Test URLs:

1. **Form Access:**
   ```
   https://www.wechechaconstruction.et/employees/create
   ```
   **Should Show:** 6-step progress indicator

2. **Step 5 Check:**
   ```
   https://www.wechechaconstruction.et/employees/create?step=5
   ```
   **Should Show:** Educational Background form

3. **Step 6 Check:**
   ```
   https://www.wechechaconstruction.et/employees/create?step=6
   ```
   **Should Show:** Work Experience form

---

## 📝 Test Checklist

### Functional Tests:

- [ ] Navigate to `/employees/create`
- [ ] Complete Steps 1-4 (existing flow)
- [ ] **Step 5**: Add education record
- [ ] **Step 5**: Upload certificate photo (JPG/PNG)
- [ ] **Step 5**: Click "Add Another Education"
- [ ] Click "Next Step" (proceed to Step 6)
- [ ] **Step 6**: Add experience record
- [ ] **Step 6**: Upload license document (PDF)
- [ ] **Step 6**: Check "Current" checkbox
- [ ] Click "Complete Registration"
- [ ] Verify employee profile shows education
- [ ] Verify employee profile shows experience
- [ ] Click "View Certificate" link
- [ ] Click "View License" link

### File Upload Tests:

- [ ] Upload JPG certificate (under 5MB)
- [ ] Upload PNG certificate (under 5MB)
- [ ] Upload PDF license (under 10MB)
- [ ] Upload JPG license (under 10MB)
- [ ] Try uploading oversized file (should fail)
- [ ] Try uploading wrong format (should fail)

---

## 🐛 Troubleshooting

### Issue: Migrations Fail

**Error:** "Table already exists"
```bash
# Check if tables exist
php artisan tinker
>>> DB::select('SHOW TABLES LIKE "employee_education"');
>>> exit

# If exists, skip migration or rollback
```

**Error:** "Connection refused"
```bash
# Check database connection in .env
cat .env | grep DB_

# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

### Issue: Storage Link Fails

**Error:** "symlink(): File exists"
```bash
# Remove old link
rm public/storage

# Recreate
php artisan storage:link
```

**Error:** "Permission denied"
```bash
# Check permissions
ls -la public/

# Fix ownership
chown -R www-data:www-data public/
```

---

### Issue: File Upload Fails

**Error:** "Maximum file size exceeded"
```bash
# Check PHP settings
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Edit php.ini
upload_max_filesize = 10M
post_max_size = 20M

# Restart web server
service apache2 restart
# OR
service nginx restart
```

**Error:** "Storage path not found"
```bash
# Check directories
ls -la storage/app/public/

# Create if missing
mkdir -p storage/app/public/employee_certificates
mkdir -p storage/app/public/employee_licenses
```

---

### Issue: Can't See New Steps

**Symptom:** Still showing 4 steps instead of 6

**Solution:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Clear browser cache
# Press Ctrl+Shift+Delete or Ctrl+F5
```

---

### Issue: Validation Errors

**Error:** "Education is required"
- At least one education record must be added
- Cannot proceed without education

**Error:** "File type not allowed"
- Certificates: Only JPG, PNG
- Licenses: Only PDF, JPG, PNG

**Error:** "File too large"
- Certificates: Max 5MB
- Licenses: Max 10MB

---

## 🔍 Database Verification

### Check Tables Created:
```sql
-- Via MySQL/MariaDB
USE your_database_name;
SHOW TABLES LIKE 'employee_%';

-- Should show:
-- employee_education
-- employee_experience
```

### Check Table Structure:
```sql
DESCRIBE employee_education;
DESCRIBE employee_experience;
```

### Check Sample Data (After Testing):
```sql
SELECT * FROM employee_education LIMIT 5;
SELECT * FROM employee_experience LIMIT 5;
```

---

## 📊 Success Indicators

### ✅ Installation Successful If:

1. ✓ Migrations ran without errors
2. ✓ Storage directories created
3. ✓ Symlink created (public/storage → storage/app/public)
4. ✓ Permissions set correctly (755)
5. ✓ Caches cleared
6. ✓ Form shows 6 steps
7. ✓ Can add education records
8. ✓ Can upload files
9. ✓ Files accessible via profile
10. ✓ No console errors

---

## 🎯 Production Deployment Summary

### Minimum Required Steps:
```bash
# 1. Upload all files
# 2. Run these 5 commands:

php artisan migrate
php artisan storage:link
mkdir -p storage/app/public/employee_certificates
mkdir -p storage/app/public/employee_licenses
php artisan cache:clear

# 3. Test the form
# 4. Done!
```

### Recommended Steps:
```bash
# Include permission fixes
chmod -R 755 storage/app/public
chown -R www-data:www-data storage/app/public

# Include all cache clearing
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

---

## 📞 Support Commands

### If Something Goes Wrong:

```bash
# 1. Check Laravel logs
tail -f storage/logs/laravel.log

# 2. Check web server logs
tail -f /var/log/apache2/error.log
# OR
tail -f /var/log/nginx/error.log

# 3. Check PHP errors
php -l app/Http/Controllers/EmployeeController.php

# 4. Verify environment
php artisan env
php artisan about

# 5. Reset everything
php artisan migrate:fresh  # WARNING: Deletes all data!
php artisan storage:link
php artisan cache:clear
```

---

## ✨ Post-Deployment

### After Successful Deployment:

1. ✅ Notify HR team
2. ✅ Train staff on new steps
3. ✅ Test with real data
4. ✅ Monitor file storage usage
5. ✅ Setup backup for uploaded documents
6. ✅ Schedule license expiry monitoring

---

## 📋 Backup Recommendations

### Files to Backup:
```
storage/app/public/employee_certificates/
storage/app/public/employee_licenses/
```

### Database Tables to Backup:
```
employee_education
employee_experience
```

### Backup Commands:
```bash
# Backup files
tar -czf employee_docs_backup_$(date +%Y%m%d).tar.gz \
    storage/app/public/employee_certificates \
    storage/app/public/employee_licenses

# Backup database tables
mysqldump -u user -p database_name \
    employee_education employee_experience \
    > employee_tables_backup_$(date +%Y%m%d).sql
```

---

## 🎉 Deployment Complete!

Once all checks pass:
- ✅ Module is live
- ✅ Users can create employees with education/experience
- ✅ Files are uploading correctly
- ✅ Profiles display correctly

**Status:** READY FOR PRODUCTION USE 🚀

---

**Deployment Date:** _____________

**Deployed By:** _____________

**Verified By:** _____________

**Notes:** 
_______________________________________________________________________
_______________________________________________________________________
_______________________________________________________________________
