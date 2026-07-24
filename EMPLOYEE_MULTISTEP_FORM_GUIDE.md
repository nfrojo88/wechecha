# Multi-Step Employee Creation Form with Asset Assignment

## Overview

The employee creation form has been enhanced with a **4-step wizard** that guides HR officers through employee registration and asset assignment in a structured workflow.

---

## Form Structure

### **Step 1: Basic Information** ✓
Collects personal details:
- Employee Code (auto-generated)
- Full Name
- Primary Phone Number (required)
- Email Address
- Department (required)
- Designation / Role (required)

### **Step 2: Employment Details** ✓
Employment-specific information:
- Employment Type (Permanent, Contract, Daily Worker)
- Contract Start Date (required)
- Employment Status (Active, Suspended, Terminated)
- Assigned Project (optional)
- Site Assignment (HQ, Site A, Site B, etc.)

### **Step 3: Salary Information** ✓
Financial and banking details:
- Monthly Base Salary (ETB)
- Contract Type (Full-Time, Part-Time, Temporary)
- Bank Name
- Bank Account Number

### **Step 4: Asset Assignment** ✓
Link equipment to employee:
- Material / Equipment selection
- Asset Type (Computer, Tools, etc.)
- Category (Office, Field, etc.)
- Unit Price
- Select multiple assets with checkboxes

---

## Features

### **Progress Indicator**
- Visual step counter (1, 2, 3, 4)
- Color-coded states:
  - **Blue**: Current step
  - **Green**: Completed steps
  - **Gray**: Future steps
- Connected with progress line

### **Navigation**
- **Next Step**: Advance to next form section
- **Previous**: Go back to edit previous section
- **Complete Registration**: Submit entire form on step 4

### **Asset Assignment**
- Checkboxes to select multiple assets
- "Select All" option for bulk selection
- Displays asset details:
  - Asset name
  - Asset type
  - Category
  - Availability status
  - Unit price (in Br)

### **Database Tracking**
- All assets linked to employee in `employee_assets` table
- Tracks assignment date, status, and notes
- Supports asset returns and damage reporting

---

## Database Schema

### Employee Assets Table
```sql
CREATE TABLE employee_assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    assigned_date DATE DEFAULT CURRENT_DATE,
    returned_date DATE NULL,
    status ENUM('assigned', 'in_use', 'returned', 'damaged') DEFAULT 'assigned',
    notes LONGTEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY employee_asset_unique (employee_id, product_id),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX employee_id (employee_id),
    INDEX product_id (product_id)
);
```

---

## Models & Relationships

### **Employee Model**
```php
public function assets()
{
    return $this->hasMany(EmployeeAsset::class);
}

public function activeAssets()
{
    return $this->assets()->whereIn('status', ['assigned', 'in_use']);
}
```

### **EmployeeAsset Model**
```php
// Relationships
public function employee() { }
public function product() { }

// Methods
public function isActive()
public function returnAsset($notes = null)
public function markDamaged($notes = null)
```

### **Product Model**
Existing model - no changes needed (used as assets table)

---

## File Changes

### **Modified Files**
1. `resources/views/hr/employees/create.blade.php`
   - Complete redesign with 4-step wizard
   - Progress indicator
   - Asset selection table
   - Navigation buttons

2. `app/Http/Controllers/EmployeeController.php`
   - Enhanced `create()` method
   - Enhanced `store()` method with asset linking
   - Validation for asset assignments

3. `app/Models/Employee.php`
   - Added `assets()` relationship
   - Added `activeAssets()` method

### **New Files Created**
1. `app/Models/EmployeeAsset.php`
   - Asset assignment model
   - Return/damage tracking
   - Status management

2. `database/migrations/2026_07_10_create_employee_assets_table.php`
   - Employee assets table
   - Relationships and indexes

---

## Usage Workflow

### **Creating an Employee with Assets**

1. **Navigate to**: `/employees/create`

2. **Step 1 - Basic Info**
   - Enter employee code (auto-generated)
   - Fill name, phone, email
   - Select department & role
   - Click "Next Step"

3. **Step 2 - Employment**
   - Choose employment type
   - Set contract start date
   - Select status and project
   - Click "Next Step"

4. **Step 3 - Salary**
   - Enter monthly salary
   - Add bank details
   - Click "Next Step"

5. **Step 4 - Assets**
   - Select assets (computers, tools, etc.)
   - Use "Select All" checkbox for multiple
   - Click "Complete Registration"

6. **Result**
   - Employee created
   - Assets linked automatically
   - Redirects to employee profile

---

## Asset Management

### **View Employee Assets**
```php
$employee->assets()->get();
$employee->activeAssets()->get();
```

### **Return an Asset**
```php
$asset = EmployeeAsset::find($id);
$asset->returnAsset('Equipment in good condition');
```

### **Report Damage**
```php
$asset = EmployeeAsset::find($id);
$asset->markDamaged('Screen damaged - needs replacement');
```

---

## Frontend Validation

### **Step Navigation**
- Form data persists between steps using session/form values
- Previous step preserved when moving forward
- Back button restores previous form data

### **Checkboxes**
- Individual asset selection
- "Select All" toggles all checkboxes
- Prevents duplicate asset assignment (unique constraint)

### **Responsive Design**
- Works on desktop, tablet, mobile
- Progress indicator adapts to screen size
- Asset table is scrollable on small screens

---

## Backend Validation

### **Employee Validation**
- Employee code must be unique
- Required fields enforced
- Salary must be numeric and >= 0
- Phone and email optional but validated if provided

### **Asset Validation**
- Assets array must contain valid product IDs
- Each product must exist in database
- Unique constraint prevents duplicate assignments

---

## Styling

### **Progress Indicator**
- Blue: Active step
- Green: Completed steps
- Gray: Pending steps
- Connected with visual line

### **Step Content**
- Icon prefix for each step section
- Color-coded icons:
  - Blue (user-circle): Personal
  - Green (briefcase): Employment
  - Orange (money-bill): Salary
  - Blue (computer): Assets

### **Buttons**
- Primary blue for Next
- Danger red for Previous
- Success green for Complete

---

## API Integration

### **Get Employee with Assets**
```php
GET /employees/{id}
Response includes:
{
    "id": 1,
    "full_name": "Abebe Bikila",
    "assets": [
        {
            "id": 1,
            "product_id": 5,
            "status": "assigned",
            "assigned_date": "2026-07-10"
        }
    ]
}
```

---

## Security & Permissions

- Gate authorization on `create` action
- User must have `hr.employees.create` permission
- Asset assignments validated before saving
- Unique constraint prevents duplicate device assignments

---

## Deployment Steps

1. **Run Migration**
   ```bash
   php artisan migrate
   ```

2. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

3. **Test**
   - Navigate to `/employees/create`
   - Complete all 4 steps
   - Verify employee created with assets

---

## Future Enhancements

- [ ] Asset return form in employee profile
- [ ] Asset damage reporting
- [ ] Asset depreciation tracking
- [ ] Asset audit reports
- [ ] Email notification on asset assignment
- [ ] Asset inventory dashboard
- [ ] Bulk asset assignment
- [ ] Asset handover forms

---

## Troubleshooting

### **Assets not appearing in dropdown**
- Verify products exist in database
- Check `status = 'active'` filter in controller

### **Duplicate asset error**
- Unique constraint prevents re-assigning same asset
- Return asset first before re-assignment

### **Form not progressing**
- Check browser console for JavaScript errors
- Verify session storage is working
- Clear browser cache and cookies

---

## Support

For issues or questions about the multi-step form:
1. Check the EmployeeController store method
2. Verify EmployeeAsset model relationships
3. Review migration file for table structure
4. Check blade template for form structure

