# Employee Education & Experience Module - Complete Setup Guide

## ✅ What Was Added

This module adds two new steps to the employee creation form:
- **Step 5: Educational Background** - Add education records with certificate photo uploads
- **Step 6: Work Experience** - Add work history with professional license document uploads

## 📁 Files Created/Modified

### Database Migrations
1. `database/migrations/2024_01_20_000001_create_employee_education_table.php`
   - Stores educational qualifications
   - Fields: degree_level, field_of_study, institution_name, location, dates, GPA, certificate_photo

2. `database/migrations/2024_01_20_000002_create_employee_experience_table.php`
   - Stores work history and professional licenses
   - Fields: job_title, company_name, location, dates, responsibilities, references, license documents

### Models
1. `app/Models/EmployeeEducation.php`
   - Education record model with certificate URL accessor
   - Duration calculations

2. `app/Models/EmployeeExperience.php`
   - Experience record model with license URL accessor
   - Duration calculations, license expiry checking

3. `app/Models/Employee.php` (Updated)
   - Added `education()` relationship
   - Added `experience()` relationship

### Controllers
1. `app/Http/Controllers/EmployeeController.php` (Updated)
   - Updated to handle 6-step form (was 4 steps)
   - Added file upload handling for certificates and licenses
   - Added validation for education and experience data
   - Methods added:
     - `storeEducationFilesInSession()` - Temporary storage during navigation
     - `storeExperienceFilesInSession()` - Temporary storage during navigation
     - `saveEducationRecords()` - Save education with file uploads
     - `saveExperienceRecords()` - Save experience with file uploads

### Views
1. `resources/views/hr/employees/create.blade.php` (Updated)
   - Added Step 5: Educational Background form
   - Added Step 6: Work Experience form
   - Multiple education/experience entries with add/remove functionality
   - File upload fields for certificates (JPG/PNG) and licenses (PDF/Images)
   - Updated progress indicator to show 6 steps

2. `resources/views/hr/employees/show.blade.php` (Updated)
   - Added Educational Background section with certificate links
   - Added Work Experience & Licenses section with document links
   - Added Qualifications summary card showing:
     - Total education records
     - Total work experience positions
     - Total years of experience calculated

## 🚀 Installation Steps

### Step 1: Run Database Migrations

```bash
php artisan migrate
```

This will create:
- `employee_education` table
- `employee_experience` table

### Step 2: Create Storage Directories

```bash
php artisan storage:link
```

Make sure the following directories exist in `storage/app/public/`:
- `employee_certificates/` - For education certificate photos
- `employee_licenses/` - For professional license documents
- `temp/education/` - Temporary storage during form navigation
- `temp/experience/` - Temporary storage during form navigation

If they don't exist automatically, create them:

```bash
mkdir storage/app/public/employee_certificates
mkdir storage/app/public/employee_licenses
mkdir storage/app/public/temp
mkdir storage/app/public/temp/education
mkdir storage/app/public/temp/experience
```

### Step 3: Set File Upload Limits (Optional)

In your `.env` file, ensure adequate upload limits:

```env
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=20M
```

In `php.ini`:
```ini
upload_max_filesize = 10M
post_max_size = 20M
max_file_uploads = 20
```

### Step 4: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## 📋 Features

### Educational Background (Step 5)
- **Multiple Entries**: Add unlimited education records
- **Degree Levels**: PhD, Master, Bachelor, Diploma, Certificate, High School
- **Fields**:
  - Degree Level *
  - Field of Study *
  - Institution Name *
  - Location
  - Start Date / End Date
  - Grade / GPA
  - Description / Achievements
  - Certificate Photo Upload (JPG, PNG - Max 5MB)
- **Verification Status**: Track verified vs unverified qualifications

### Work Experience (Step 6)
- **Multiple Entries**: Add unlimited experience records
- **Current Employment**: Checkbox to mark current position
- **Fields**:
  - Job Title *
  - Company Name *
  - Location
  - Start Date * / End Date
  - Key Responsibilities
  - Reference Name / Phone
  - Professional License Number
  - License Expiry Date
  - License Document Upload (PDF, JPG, PNG - Max 10MB)
- **Duration Calculation**: Automatic calculation of employment duration
- **License Expiry Tracking**: Visual indicators for expired licenses

### Employee Profile Enhancements
- **Educational Background Section**: 
  - Shows all education records
  - Certificate photo links
  - Verification badges
  - Timeline view

- **Work Experience & Licenses Section**:
  - Shows all work history
  - License document links
  - License validity status
  - Reference information

- **Qualifications Summary Card**:
  - Total education records count
  - Total work positions count
  - Total years of experience

## 🔒 File Storage Structure

```
storage/app/public/
├── employee_certificates/    # Education certificate photos
│   ├── 1234567890_certificate.jpg
│   └── ...
├── employee_licenses/        # Professional license documents
│   ├── 1234567890_license.pdf
│   └── ...
└── temp/                     # Temporary storage during form navigation
    ├── education/
    └── experience/
```

## 📝 Usage Instructions

### For Employees/HR:

1. **Navigate to Employee Creation**:
   - Go to `/employees/create`

2. **Complete Steps 1-4** (as before):
   - Step 1: Basic Information
   - Step 2: Employment Details
   - Step 3: Salary Information
   - Step 4: Asset Assignment

3. **Step 5: Add Educational Background**:
   - Fill in at least one education record
   - Upload certificate photo (optional but recommended)
   - Click "Add Another Education" for multiple degrees
   - Click "Next Step"

4. **Step 6: Add Work Experience**:
   - Fill in work history (optional)
   - Check "Current" if still employed there
   - Add reference information if available
   - Upload professional license document if applicable
   - Click "Add Another Experience" for multiple positions
   - Click "Complete Registration"

5. **View Employee Profile**:
   - Navigate to employee profile to see education and experience
   - Click "View Certificate" or "View License" to open documents

### For Developers:

**Access Education Records:**
```php
$employee = Employee::find(1);
$education = $employee->education; // All education records
$latestDegree = $employee->education()->latest('end_date')->first();
```

**Access Experience Records:**
```php
$experience = $employee->experience; // All experience records
$currentJob = $employee->experience()->where('is_current', true)->first();
$totalExperience = $employee->experience->sum(function($exp) {
    return $exp->start_date->diffInMonths($exp->end_date ?? now());
});
```

**Check License Status:**
```php
foreach($employee->experience as $exp) {
    if($exp->license_expiry && $exp->is_license_expired) {
        echo "License expired for {$exp->job_title}";
    }
}
```

## 🎨 File Upload Specifications

### Education Certificate Photos:
- **Formats**: JPEG, PNG, JPG
- **Max Size**: 5MB (5120 KB)
- **Storage**: `storage/app/public/employee_certificates/`
- **Recommended**: Photo or scan of degree/certificate

### Professional License Documents:
- **Formats**: PDF, JPEG, PNG, JPG
- **Max Size**: 10MB (10240 KB)
- **Storage**: `storage/app/public/employee_licenses/`
- **Recommended**: PDF for text documents, images for ID cards

## 🔍 Validation Rules

### Education (Step 5):
- `degree_level`: Required
- `field_of_study`: Required
- `institution_name`: Required
- `certificate_photo`: Optional, Image (jpg, png), Max 5MB

### Experience (Step 6):
- `job_title`: Required if experience provided
- `company_name`: Required if experience provided
- `start_date`: Required if experience provided
- `license_document`: Optional, File (pdf, jpg, png), Max 10MB

## 🐛 Troubleshooting

### File Upload Errors

**"Maximum upload file size exceeded"**
- Check `.env` and `php.ini` settings
- Increase `upload_max_filesize` and `post_max_size`

**"Storage path not found"**
- Run `php artisan storage:link`
- Check directory permissions (755 for directories, 644 for files)

**"Validation failed for education/experience"**
- At least one education record is required
- Experience is optional but validates if provided

### Migration Errors

**"Table already exists"**
- Tables may have been created before
- Check with: `php artisan migrate:status`
- If needed, rollback: `php artisan migrate:rollback`

## 📊 Database Schema

### employee_education Table:
```sql
- id
- employee_id (foreign key)
- degree_level
- field_of_study
- institution_name
- location
- start_date
- end_date
- grade_gpa
- description
- certificate_photo (file path)
- is_verified (boolean)
- timestamps
```

### employee_experience Table:
```sql
- id
- employee_id (foreign key)
- job_title
- company_name
- location
- start_date
- end_date
- is_current (boolean)
- responsibilities
- reference_name
- reference_phone
- license_document (file path)
- license_number
- license_expiry
- timestamps
```

## ✨ Future Enhancements

Potential additions:
- Education verification workflow
- License renewal reminders
- Experience verification from references
- Skill extraction from experience
- Timeline visualization
- Document approval system
- Bulk document upload
- Integration with external verification services

## 🎯 Summary

This module provides a comprehensive solution for tracking employee education and work experience, complete with document management. All files are properly stored, validated, and displayed in the employee profile.

**Key Benefits:**
- ✅ Complete educational history tracking
- ✅ Work experience documentation
- ✅ Professional license management
- ✅ Document storage and retrieval
- ✅ License expiry tracking
- ✅ Reference information storage
- ✅ User-friendly multi-step form
- ✅ Responsive design
- ✅ Easy to extend and customize

---

**Status:** ✅ Complete and Ready to Use
**Version:** 1.0
**Last Updated:** {{ date('Y-m-d') }}
