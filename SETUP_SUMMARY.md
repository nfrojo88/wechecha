# 🎉 Employee Education & Experience Module - Quick Start

## What's New?

Added **2 new steps** to employee creation form:
- **Step 5**: Educational Background (with certificate photos)
- **Step 6**: Work Experience (with professional license documents)

## 🚀 Quick Setup (3 Commands)

```bash
# 1. Run migrations to create database tables
php artisan migrate

# 2. Link storage for file uploads
php artisan storage:link

# 3. Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

## ✅ What You Can Do Now

### For HR/Admins:
1. Go to: `https://www.wechechaconstruction.et/employees/create`
2. Complete all 6 steps:
   - Step 1-4: Basic info, Employment, Salary, Assets (as before)
   - **Step 5**: Add education (degree, institution, certificate photo)
   - **Step 6**: Add work experience (job history, professional licenses)
3. View complete employee profiles with education and experience

### Key Features:
- ✅ Multiple education records per employee
- ✅ Multiple work experience entries per employee
- ✅ Upload certificate photos (JPG/PNG, max 5MB)
- ✅ Upload license documents (PDF/Image, max 10MB)
- ✅ Track professional license expiry dates
- ✅ Store reference contacts
- ✅ Calculate total years of experience
- ✅ Verify educational qualifications

## 📁 Files Created

### Database:
- `employee_education` table - stores education records
- `employee_experience` table - stores work history

### Storage Folders:
- `storage/app/public/employee_certificates/` - certificate photos
- `storage/app/public/employee_licenses/` - license documents

## 🔧 Optional: Increase Upload Limits

If you need larger file uploads, add to `.env`:
```env
UPLOAD_MAX_FILESIZE=10M
POST_MAX_SIZE=20M
```

## 📖 Full Documentation

See `EMPLOYEE_EDUCATION_EXPERIENCE_SETUP.md` for:
- Detailed usage instructions
- Database schema
- Troubleshooting guide
- Code examples
- API documentation

## 🎯 Test It Out

1. Create a new employee
2. Add education (e.g., Bachelor in Civil Engineering from AAU)
3. Upload certificate photo
4. Add work experience (e.g., Site Engineer at XYZ Construction)
5. Upload professional license
6. View employee profile to see everything displayed beautifully!

---

**Ready to use!** ✅ All features are working and tested.

For detailed documentation, read: `EMPLOYEE_EDUCATION_EXPERIENCE_SETUP.md`
