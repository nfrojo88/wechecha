# Quick Start Guide - HR Employee Asset Management System

## 🚀 Deploy in 5 Minutes

### Step 1: Run Migrations (1 min)
```bash
cd /path/to/construct-pro-erp
php artisan migrate --force
```

This creates the `employee_assets` table with all required columns and relationships.

### Step 2: Login & Access (1 min)
1. Login as HR Officer or HR Manager
2. Look for **"Asset Management"** link in HR Manager sidebar section

### Step 3: Create Your First Employee (2 min)
1. Click **"Create Employee"** from sidebar
2. **Step 1**: Enter basic info
   - Code: EMP-001
   - Name: Your Employee Name
   - Department: Civil Engineering
   - Role: Site Engineer
3. **Step 2**: Employment details
   - Type: Permanent
   - Date: Today
   - Status: Active
4. **Step 3**: Salary
   - Base Salary: 25,000
   - Leave bank details empty (optional)
5. **Step 4**: Assets
   - Select computers, tools, or equipment
   - Click "Select All" to assign multiple
6. **Click "Complete Registration"**

### Step 4: View Your Employee (1 min)
1. You'll be redirected to employee profile
2. Scroll down to see **"Assigned Assets & Equipment"**
3. View all linked assets with action buttons

### Step 5: Test Asset Operations (Done!)
- **Return Asset**: Click return icon, fill form, submit
- **Report Damage**: Click damage icon, report issue, submit
- **View Dashboard**: Click "Asset Management" to see all metrics

---

## 📊 Key URLs

| Feature | URL |
|---------|-----|
| Create Employee | `/employees/create` |
| Employee Profile | `/employees/{id}` |
| Asset Dashboard | `/assets/dashboard` |
| Utilization Report | `/asset-reports/utilization` |
| Damage Report | `/asset-reports/damage` |
| Employee Allocation | `/asset-reports/employee-allocation` |

---

## 📋 What You Can Do Now

### Daily Tasks
- ✅ Create new employees with assets
- ✅ View employee profiles with assigned equipment
- ✅ Return assets when employees leave
- ✅ Report damaged equipment
- ✅ Track asset lifecycle

### Analytics
- ✅ Dashboard with KPI cards
- ✅ Asset value distribution
- ✅ Department-wise asset breakdown
- ✅ Recent activity timeline

### Reports
- ✅ Utilization Report (daily metrics)
- ✅ Damage Report (incidents & statistics)
- ✅ Employee Allocation (who has what)
- ✅ CSV exports for Excel analysis

---

## 🎯 Common Workflows

### Workflow 1: New Employee Onboarding
```
1. Go to /employees/create
2. Enter details (Steps 1-3)
3. Select assets (Step 4)
4. Submit
5. Profile shows assigned equipment
```

### Workflow 2: Asset Return
```
1. View employee profile
2. Find asset in "Assigned Assets & Equipment"
3. Click return button
4. Enter return details
5. Asset status → "Returned"
6. Appears in "Asset History" tab
```

### Workflow 3: Damage Report
```
1. View employee profile
2. Click damage button on asset
3. Enter severity & cause
4. Describe damage
5. Asset status → "Damaged"
6. Appears in reports
```

### Workflow 4: View Damage Statistics
```
1. From sidebar: Asset Management
2. Click Reports dropdown
3. Select "Damage Report"
4. View stats & incidents
5. Download CSV
```

---

## ❓ Frequently Asked Questions

**Q: Can I assign multiple assets to one employee?**
A: Yes! In Step 4 of employee creation, select multiple assets using checkboxes.

**Q: What if I forgot to assign assets?**
A: You can manually link assets later by editing the employee record (feature coming soon).

**Q: How do I export asset data?**
A: Click the dropdown "Reports" on the Asset Dashboard to export utilization, damage, or allocation reports as CSV.

**Q: Can I see all damaged assets?**
A: Yes! Go to Asset Management → Reports → Damage Report to see all incidents with statistics.

**Q: What's the difference between "Assigned" and "In Use"?**
A: Both mean the asset is with the employee. "In Use" is for tracking active usage. You can update in the database if needed.

**Q: Can employees see their own assets?**
A: Not yet, but this can be added to the Employee Self-Service Portal.

---

## ✅ Checklist Before Go-Live

- [ ] Migrations run successfully
- [ ] Sidebar menu items visible
- [ ] Can create new employee
- [ ] Can assign assets during creation
- [ ] Can view employee profile
- [ ] Can return asset
- [ ] Can report damage
- [ ] Can view asset dashboard
- [ ] Can generate reports
- [ ] Can export CSV files

---

## 📞 Support

**If migrations fail:**
```bash
# Check migration status
php artisan migrate:status

# Rollback and retry
php artisan migrate:rollback
php artisan migrate --force
```

**If menu items don't appear:**
- Clear cache: `php artisan cache:clear`
- Check user role: Must be hr_officer or hr_manager
- Verify routes: `php artisan route:list | grep asset`

**If assets don't appear in Step 4:**
- Verify products exist in database
- Check products have status='active'
- Ensure products have unit_cost values

---

## 🎓 Training Tips

### For HR Officers
1. Create sample employees with assets
2. Practice return workflows
3. Generate damage reports weekly
4. Monitor asset dashboard for high-value items

### For Managers
1. Use Asset Dashboard for asset overview
2. Review damage reports monthly
3. Export allocation reports for budget planning
4. Track asset turnover

### For Admins
1. Monitor database for new assignments
2. Ensure products are properly categorized
3. Review reports for anomalies
4. Schedule backup procedures

---

## 📈 Success Metrics

- **Track**: Asset assignment rate (% of employees with assets)
- **Monitor**: Damage incident rate (damaged assets / total assets)
- **Report**: Asset utilization (active assets / total assets)
- **Analyze**: Asset value distribution by department

---

## 🔄 Workflow Diagram

```
Employee Creation
    ↓
[Step 1-3: Basic Info] → [Step 4: Select Assets]
    ↓
Employee Profile (with assets shown)
    ↓
    ├→ Return Asset → Asset History (Returned)
    ├→ Report Damage → Asset History (Damaged)
    └→ View Dashboard → Analytics & Reports
```

---

## ⚡ Pro Tips

1. **Use "Select All"** in Step 4 to quickly assign standard assets
2. **Export reports** monthly for auditing and budgeting
3. **Check dashboard** weekly to monitor asset status
4. **Review damage reports** to identify problem areas
5. **Archive old data** quarterly to keep system fast

---

## 🎉 You're Ready!

The system is now ready to use. Start with:

1. Create an employee with assets
2. View the profile
3. Practice returning/damaging an asset
4. View the asset dashboard
5. Generate a report

**Enjoy! The system will help you manage employee equipment efficiently.**

---

**Questions?** Refer to:
- `HR_EMPLOYEE_ASSET_SYSTEM_GUIDE.md` - Full documentation
- `SYSTEM_COMPLETION_SUMMARY.md` - What was built
- Individual blade files - Code comments

---

*HR Employee Asset Management System*
*Production Ready - July 8, 2026*
