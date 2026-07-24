# Test Guide - Multi-Step Employee Creation Form

## ✅ Testing the Fixed Form

### Test 1: Navigate Through All Steps
1. Go to `/employees/create`
2. Fill **Step 1**:
   - Employee Code: `EMP-2024-001`
   - Full Name: `Test Employee`
   - Phone: `0911223344`
   - Department: `Civil`
   - Role: `Site Engineer`
3. Click **"Next Step"** button
4. **Expected Result**: Should navigate to Step 2 with all Step 1 data preserved
5. Verify all fields show your entered data

### Test 2: Verify Step 2 Data Persistence
1. On Step 2, fill:
   - Employment Type: `Permanent`
   - Start Date: Select today
   - Status: `Active`
   - Project: `Select any project`
2. Click **"Next Step"**
3. **Expected Result**: Navigate to Step 3
4. Click **"Previous Step"**
5. **Expected Result**: Navigate back to Step 2 with all Step 2 data still there

### Test 3: Complete All Steps
1. On Step 2, click **"Next Step"**
2. On Step 3, fill:
   - Salary: `25000`
   - Bank Name: `Commercial Bank of Ethiopia`
   - Account: `1000123456789`
3. Click **"Next Step"**
4. On Step 4, select **at least 1 asset** from the list
5. Click **"Complete Registration"**
6. **Expected Result**: 
   - Form submits
   - Employee is created
   - Redirected to employee profile showing:
     - All entered information
     - Assigned assets in "Assigned Assets & Equipment" section
     - Asset status shows "Assigned"

### Test 4: Validation on Each Step
1. Go to `/employees/create`
2. Leave **Employee Code** empty
3. Click **"Next Step"**
4. **Expected Result**: 
   - Form stays on Step 1
   - Shows error: "Employee Code field is required"
   - All other entered data is preserved

### Test 5: Navigation Buttons
- **Step 1**: Should see only "Next Step" button (no Previous)
- **Step 2-3**: Should see both "Previous" and "Next Step" buttons
- **Step 4**: Should see "Previous" and "Complete Registration" buttons

### Test 6: Back Button (Browser Back)
1. Complete navigation to Step 3
2. Click browser back button
3. **Expected Result**: Should return to Step 2 with data intact (or Step 1 depending on browser cache)

## ✅ Success Criteria

- [ ] Data persists when clicking "Next Step"
- [ ] Data persists when clicking "Previous Step"
- [ ] Validation works for each step
- [ ] Can complete form from Step 1 to Step 4 without losing data
- [ ] Assets are assigned when employee is created
- [ ] Employee profile shows all entered information
- [ ] Employee profile shows assigned assets

## 🐛 Troubleshooting

**Problem**: Data is lost when clicking "Next Step"
- **Solution**: Clear browser cache, refresh page, try again
- **Check**: Server-side: Verify session is working (`php artisan tinker` then `session()`)

**Problem**: "Employee Code already exists" error after completing form
- **Solution**: This is expected if you use same code twice. Use different code.
- **Check**: Employee codes must be unique

**Problem**: Assets don't appear on Step 4
- **Solution**: Create products first in Products section
- **Check**: Products must exist with `unit_cost` values

**Problem**: Validation error shows but data is preserved on same step
- **Expected Behavior**: This is correct - form re-renders with error message
- **Action**: Fix the error and try again

## 📋 Sample Test Data

### Employee 1
- Code: `EMP-2024-001`
- Name: `Abebe Bikila`
- Phone: `0911111111`
- Department: `Civil`
- Role: `Site Engineer`
- Type: `Permanent`
- Start Date: `2024-07-08`
- Status: `Active`
- Salary: `30000`
- Bank: `CBE`
- Account: `1000111111111`

### Employee 2
- Code: `EMP-2024-002`
- Name: `Marta Tekle`
- Phone: `0922222222`
- Department: `Admin`
- Role: `HR Officer`
- Type: `Contract`
- Start Date: `2024-06-01`
- Status: `Active`
- Salary: `18000`
- Bank: `Dashen Bank`
- Account: `1000222222222`

## ✅ All Tests Passed
When you've completed all tests above and they pass, the multi-step form is working correctly!

---

**Last Updated**: July 8, 2026
**Status**: ✅ Form Fixed and Ready for Testing
