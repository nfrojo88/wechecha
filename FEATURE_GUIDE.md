# 📚 Employee Education & Experience Module - Feature Guide

## 🎯 Overview

This module extends the employee registration process from 4 steps to **6 steps**, adding comprehensive tracking of:
- Educational qualifications with certificate uploads
- Work experience history with professional license documentation

---

## 📋 Form Flow

```
Step 1: Basic Info → Step 2: Employment → Step 3: Salary → Step 4: Assets → 
Step 5: Education → Step 6: Experience → Complete! ✅
```

---

## 🎓 Step 5: Educational Background

### What You Can Add:
- **Degree Level**: PhD, Master, Bachelor, Diploma, Certificate, High School
- **Field of Study**: e.g., Civil Engineering, Business Administration
- **Institution**: University or school name
- **Location**: City, country
- **Duration**: Start and end dates
- **GPA/Grade**: e.g., 3.8/4.0, Distinction, First Class
- **Description**: Thesis title, honors, achievements
- **Certificate Photo**: Upload degree/certificate image (JPG/PNG, max 5MB)

### Features:
- ✅ Add multiple education records
- ✅ Click "Add Another Education" for more entries
- ✅ Remove unwanted entries
- ✅ At least one education record required
- ✅ Certificate upload optional but recommended

### Example Entry:
```
Degree: Bachelor's Degree
Field: Civil Engineering
Institution: Addis Ababa University
Location: Addis Ababa, Ethiopia
Duration: 2015 - 2019
GPA: 3.7/4.0
Description: Graduated with honors. Final year project on sustainable construction.
Certificate: [Upload bachelor_certificate.jpg]
```

---

## 💼 Step 6: Work Experience & Professional Licenses

### What You Can Add:

#### Work History:
- **Job Title**: e.g., Site Engineer, Project Manager
- **Company Name**: e.g., ABC Construction
- **Location**: City, country
- **Duration**: Start and end dates
- **Current Position**: Check if still working there
- **Responsibilities**: Key duties and achievements

#### Reference (Optional):
- **Reference Name**: Former supervisor or colleague
- **Reference Phone**: Contact number

#### Professional License (Optional):
- **License Number**: e.g., PE-12345
- **Expiry Date**: When license expires
- **License Document**: Upload PDF or image (max 10MB)

### Features:
- ✅ Add multiple work experiences
- ✅ Click "Add Another Experience" for more entries
- ✅ "Current" checkbox disables end date
- ✅ All fields optional (can skip if no experience)
- ✅ Upload license documents (PDF preferred)

### Example Entry:
```
Job Title: Site Engineer
Company: Wechecha Construction
Location: Addis Ababa, Ethiopia
Start Date: 2019-07-01
End Date: (Current) ✓
Responsibilities: Supervised construction of residential buildings, 
managed site teams, ensured quality standards...

Reference Name: Ato Bekele Desta
Reference Phone: +251 911 234 567

License Number: CE-2020-1234
License Expiry: 2025-12-31
License Document: [Upload professional_engineer_license.pdf]
```

---

## 👤 Employee Profile Display

### What Appears on Profile:

#### Educational Background Section:
```
┌─────────────────────────────────────────────┐
│ 🎓 Educational Background       [2 Records] │
├─────────────────────────────────────────────┤
│ 🏆 Bachelor in Civil Engineering ✅ Verified│
│    Addis Ababa University                   │
│    📍 Addis Ababa, Ethiopia                 │
│    📅 2015 - 2019                           │
│    ⭐ Grade: 3.7/4.0                        │
│    [View Certificate] 🖼                     │
└─────────────────────────────────────────────┘
```

#### Work Experience Section:
```
┌─────────────────────────────────────────────┐
│ 💼 Work Experience & Licenses  [3 Positions]│
├─────────────────────────────────────────────┤
│ 👔 Site Engineer              [Current] 🟢  │
│    Wechecha Construction                    │
│    📍 Addis Ababa, Ethiopia                 │
│    📅 Jul 2019 - Present (5 years, 6 months)│
│                                             │
│    📜 Professional License:                 │
│    License #: CE-2020-1234                  │
│    Expiry: 31 Dec 2025  [Valid] ✅          │
│    [View License] 📄                        │
│                                             │
│    ✓ Reference: Ato Bekele (+251 911...)   │
└─────────────────────────────────────────────┘
```

#### Qualifications Summary (Sidebar):
```
┌──────────────────────────┐
│ 📊 Qualifications        │
├──────────────────────────┤
│ 🎓                    2  │
│ Education Records        │
├──────────────────────────┤
│ 💼                    3  │
│ Work Experience          │
├──────────────────────────┤
│ 🕐           5y 6m       │
│ Total Experience         │
└──────────────────────────┘
```

---

## 🎨 Visual Features

### Badges & Indicators:
- **Verified Education**: Green ✅ badge
- **Current Position**: Blue "Current" badge
- **Valid License**: Green "Valid" badge
- **Expired License**: Red "Expired" badge
- **Record Counts**: Numbered badges (e.g., "2 Records")

### File Links:
- **View Certificate**: Opens education certificate image
- **View License**: Opens professional license document
- Both open in new browser tab

### Color Coding:
- 🔵 **Primary**: Education/academic content
- 🟢 **Success**: Experience/work content
- 🟡 **Warning**: License/certificate sections
- 🔴 **Danger**: Expired licenses

---

## 📊 Data Calculations

### Automatic Calculations:
1. **Education Duration**: "2015 - 2019" 
2. **Experience Duration**: "5 years, 6 months" or "3 months"
3. **Total Experience**: Sum of all positions = "5y 6m"
4. **License Status**: Auto-checks if expired based on expiry date

### Smart Features:
- If "Current" checked, end date hidden
- Duration calculated automatically
- License expiry tracked and highlighted
- Total experience summed across all jobs

---

## 🔒 File Management

### Storage Locations:
```
storage/app/public/
├── employee_certificates/     ← Education certificates
│   ├── 1234567890_bachelor.jpg
│   └── 1234567890_master.jpg
│
├── employee_licenses/         ← Professional licenses
│   ├── 1234567890_pe_license.pdf
│   └── 1234567890_contractor.pdf
│
└── temp/                      ← Temporary (during form)
    ├── education/
    └── experience/
```

### File Types & Sizes:
| File Type | Formats | Max Size |
|-----------|---------|----------|
| Certificate | JPG, PNG | 5 MB |
| License | PDF, JPG, PNG | 10 MB |

### Security:
- Files stored outside web root
- Accessed via Storage facade
- Links generated dynamically
- No direct file access

---

## 🎯 Use Cases

### 1. New Graduate Employee
```
Education: Bachelor in Civil Engineering (2023)
Experience: None (can skip Step 6)
Result: Profile shows education, no experience
```

### 2. Experienced Professional
```
Education: Bachelor (2015), Master (2018)
Experience: 3 positions (2015-2023)
License: Professional Engineer (PE-2020)
Result: Complete profile with all qualifications
```

### 3. Skilled Worker
```
Education: High School (2018), Trade Certificate (2019)
Experience: 2 positions (2019-2023)
License: Contractor License (CL-2021)
Result: Profile shows vocational path
```

---

## 💡 Tips & Best Practices

### For HR/Admins:
1. ✅ Always upload certificate photos for verification
2. ✅ Request license documents from professionals
3. ✅ Verify education details before marking verified
4. ✅ Keep reference contacts up to date
5. ✅ Check license expiry dates regularly

### For Employees:
1. 📸 Scan certificates in good lighting
2. 📄 Save licenses as PDF when possible
3. 📝 Be detailed in responsibilities
4. 📞 Get permission before adding references
5. 📅 Update current position status

---

## ⚡ Quick Reference

### Keyboard Shortcuts (During Form):
- **Tab**: Move to next field
- **Shift+Tab**: Move to previous field
- **Enter**: Submit form (final step only)

### Buttons:
- **Add Another Education**: Add more degrees
- **Add Another Experience**: Add more jobs
- **Remove**: Delete an entry (if multiple)
- **Previous**: Go back to previous step
- **Next Step**: Proceed to next step
- **Complete Registration**: Save and finish

---

## 🐛 Common Issues

### "File too large"
- **Solution**: Reduce file size or compress image/PDF
- **Limit**: 5MB for certificates, 10MB for licenses

### "Invalid file type"
- **Solution**: Use JPG/PNG for certificates, PDF/JPG/PNG for licenses
- **Convert**: Use online converters if needed

### "Can't see uploaded files"
- **Solution**: Check if storage link exists
- **Fix**: Run `php artisan storage:link`

### "Education required"
- **At least one education record must be added**
- **Can skip experience (Step 6) entirely**

---

## 📞 Support

For issues or questions:
1. Check `EMPLOYEE_EDUCATION_EXPERIENCE_SETUP.md` for detailed docs
2. Check `SETUP_SUMMARY.md` for quick setup
3. Review validation messages for specific errors
4. Contact system administrator

---

**Happy Hiring! 🎉**

Build comprehensive employee profiles with complete educational and professional history!
