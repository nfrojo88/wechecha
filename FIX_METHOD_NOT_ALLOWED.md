# Fix: Method Not Allowed Error - POST to employees.create

## Problem
Error: "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."

This occurred because the form was trying to POST to `employees.create` which only accepts GET requests.

## Root Cause
- `employees.create` is a GET route (displays the form)
- The form was trying to POST to this GET route
- Need to POST to `employees.store` instead

## Solution

### Route Structure (RESTful)
```
GET    /employees/create       → create() method (display form)
POST   /employees              → store() method (process form)
GET    /employees              → index()
GET    /employees/{id}         → show()
GET    /employees/{id}/edit    → edit()
PUT    /employees/{id}         → update()
DELETE /employees/{id}         → destroy()
```

### Changed
1. **Form Action** (in `create.blade.php`):
   ```php
   <!-- Before (wrong) -->
   <form method="POST" action="{{ route('employees.create') }}">
   
   <!-- After (correct) -->
   <form method="POST" action="{{ route('employees.store') }}">
   ```

2. **Controller Logic** (in `EmployeeController.php`):
   - Updated `store()` to detect step navigation vs final submission
   - Still validates each step
   - Still persists data to session
   - Still creates employee on final submission

## How It Works Now

**Step Navigation:**
```
User fills Step 1 → Clicks "Next"
  → Form POSTs to /employees with step=1
  → Controller detects step < 4
  → Validates Step 1 data
  → Saves to session
  → Redirects to /employees/create?step=2

User fills Step 2 → Clicks "Next"
  → Form POSTs to /employees with step=2
  → Controller detects step < 4
  → Validates Step 2 data
  → Saves to session
  → Redirects to /employees/create?step=3

... continues for Step 3 ...

User selects assets Step 4 → Clicks "Complete"
  → Form POSTs to /employees with step=4
  → Controller detects step == 4
  → Validates ALL data
  → Creates employee
  → Clears session
  → Redirects to /employees/{id} (profile)
```

## Files Modified
- `resources/views/hr/employees/create.blade.php` (form action)
- `app/Http/Controllers/EmployeeController.php` (store logic)

## Status
✅ FIXED - Form now posts to correct route and navigation works properly
