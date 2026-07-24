# Slip Sequence Manager - Setup Guide

## System Overview

The Slip Sequence Manager provides professional GRN (Receiving) and SIN (Outgoing) slip management for your construction ERP system.

### Features
- **GRN (Receiving)**: Good Received Note - for supplier deliveries
- **SIN (Outgoing)**: Store Issue Note - for material transfers to sites
- **Auto-numbering**: Sequences with optional prefix (e.g., REC2100, OUT1501)
- **Book management**: Define physical slip book ranges (e.g., 2100-2150)
- **Audit trail**: Track usage, gaps, and void slips
- **Sequence validation**: Enforce that physical slip numbers match expected sequence

## Files Created

### 1. Model
- `app/Models/SlipSequence.php` - Core slip sequence model with methods:
  - `getNextSlipNumber()` - Get next numeric value
  - `formatSlipNumber()` - Format with prefix
  - `generateSlipNumber()` - Auto-increment and return formatted number
  - `getPercentageUsed()` - Calculate usage percentage
  - `getRemainingSlips()` - Count remaining slips in book
  - `isValidSlipNumber()` - Validate slip is within book range

### 2. Controller
- `app/Http/Controllers/SlipSequenceController.php` - Full CRUD + management:
  - `index()` - Dashboard with all sequences
  - `create()` - New sequence form
  - `store()` - Save sequence
  - `edit()` - Edit sequence details
  - `update()` - Update sequence
  - `deactivate()` - Pause sequence
  - `reactivate()` - Resume sequence
  - `reset()` - Admin: reset counter to start
  - `getNextSlip()` - API endpoint for next slip number

### 3. Views
- `resources/views/slip-sequences/index.blade.php` - Dashboard showing all sequences with progress bars
- `resources/views/slip-sequences/create.blade.php` - Configuration form
- `resources/views/slip-sequences/edit.blade.php` - Edit & status management

### 4. Database
- `database/migrations/2026_07_08_create_slip_sequences_table.php`
  - Columns: id, store_id, slip_type, label, prefix, book_start_no, book_end_no, current_slip_no, used_count, status, notes
  - Unique constraint: one active sequence per store + slip_type

### 5. Routes
Added to `routes/web.php` in `store-manager` group:
```php
Route::resource('slip-sequences', SlipSequenceController::class);
Route::post('slip-sequences/{slipSequence}/deactivate', ...);
Route::post('slip-sequences/{slipSequence}/reactivate', ...);
Route::post('slip-sequences/{slipSequence}/reset', ...);
Route::get('api/slip-sequences/{storeId}/{slipType}', ...);
```

### 6. Menu
Added to `resources/views/layouts/sidebar.blade.php`:
```html
<li class="sidebar-nav-item">
    <a href="{{ route('store-manager.slip-sequences.index') }}" class="sidebar-nav-link">
        <i class="fa-solid fa-stream text-info"></i>
        <span>Slip Sequences</span>
    </a>
</li>
```

### 7. Integration
Updated `app/Http/Controllers/StoreManagerController.php`:
- Added `use App\Models\SlipSequence;`
- Modified `storeSlip()` to:
  1. Check if manual slip_no provided (allow override)
  2. If not, fetch active sequence for store + slip_type
  3. Call `$sequence->generateSlipNumber()` to get auto-incremented number
  4. Throw error if no active sequence exists

## Deployment Steps

### Step 1: Run Migration
On your server, execute:
```bash
cd /path/to/construct-pro-erp
php artisan migrate --force
```

This creates the `slip_sequences` table.

**For automated migration**, visit this URL in your browser (if you have the migration route enabled):
```
https://wechechaconstruction.et/system/run-migrations
```

### Step 2: Verify Sidebar Menu
1. Login to the system
2. Navigate to Store Manager dashboard
3. You should see "Slip Sequences" menu item below "Material Catalog"

### Step 3: Configure Your First Sequence
1. Click "Slip Sequences" in the sidebar
2. Click "Configure New Sequence"
3. Fill in:
   - **Store**: Select the store
   - **Type**: Choose "Receiving (GRN)" or "Outgoing (SIN)"
   - **Label**: E.g., "Receiving (GRN)"
   - **Prefix** (optional): E.g., "REC" or "OUT" (leave blank for numeric-only)
   - **Book Start No**: E.g., 2100 (matches your physical slip book)
   - **Book End No**: E.g., 2150
   - **Notes**: Optional notes

4. Click "Save Configuration"

### Step 4: Create a Slip with Sequence
1. Navigate to Store Manager → Create Slip
2. Select store and slip type
3. **Leave "Slip No" blank** to auto-generate from sequence
4. Fill in other details and items
5. Click "Create Slip"
6. System will auto-assign the next slip number from your configured sequence

## Usage Examples

### Example 1: Receiving (GRN) Slips
**Configuration:**
- Store: Main Warehouse
- Type: Receiving (GRN)
- Label: Receiving (GRN)
- Prefix: REC
- Book: 2100-2150

**Result:**
- 1st slip: REC02100
- 2nd slip: REC02101
- 3rd slip: REC02102
- ... (continues automatically)

### Example 2: Outgoing (SIN) Slips (Numeric Only)
**Configuration:**
- Store: Site A Store
- Type: Outgoing (SIN)
- Label: Outgoing (SIN)
- Prefix: (leave blank)
- Book: 1501-1550

**Result:**
- 1st slip: 01501
- 2nd slip: 01502
- 3rd slip: 01503

## Dashboard Features

The Slip Sequences index page shows:
- ✅ All active and inactive sequences
- 📊 Usage progress bar (% of book used)
- 🔢 Next slip to be assigned
- 📈 Remaining slips in book
- 🎯 Book range details
- ⏸️ Status (Active/Inactive/Full)
- 🎛️ Quick actions (Edit, Pause/Resume)

## Key Concepts

### Slip Type
- **receive** (GRN): Goods Received Note - when materials arrive from supplier
- **send** (SIN): Store Issue Note - when materials are transferred to project sites

### Prefix
- **With prefix**: "REC2100", "OUT1501", "GRN02100"
- **Without prefix**: "2100", "1501", "02100"
- Leave empty for numeric-only numbering

### Book Range
- **Purpose**: Match your physical slip book numbers
- **Example**: If your physical book has slips 2100-2150, enter exactly those numbers
- **System will then**: Auto-assign 2100, 2101, 2102, ... 2150
- **After full**: Book status changes to "Full" and new sequence must be created

### Status
- **active**: Currently being used for new slip assignments
- **inactive**: Not being used, but can be reactivated
- **full**: All slips in this book have been assigned (cannot reactivate)

### Unique Constraint
- Only **one active sequence per store + slip_type**
- When you activate an existing sequence, others for that store+type become inactive
- Prevents confusion about which sequence to use

## Integration with Slip Creation

When storing a slip in Store Manager → Create Slip:

1. **If you provide a Slip No manually**: Uses that number (manual override)
2. **If you leave Slip No blank**:
   - System finds active sequence for store + type
   - Calls `generateSlipNumber()` which:
     - Increments `current_slip_no`
     - Formats with prefix (if configured)
     - Increments `used_count`
     - Returns formatted number
   - If no active sequence exists: Shows error

## API Endpoint

Get next available slip number via API:
```
GET /store-manager/api/slip-sequences/{storeId}/{slipType}
```

**Response:**
```json
{
  "next_slip_no": 5,
  "prefix": "REC",
  "label": "Receiving (GRN)",
  "remaining": 46,
  "percentage_used": 9.8
}
```

## Troubleshooting

### Error: "No active slip sequence configured"
- **Solution**: Go to Slip Sequences → Configure New Sequence
- Create a sequence for that store + type
- Make sure Status = "active"

### Error: "Book range already exists"
- **Cause**: You're trying to create duplicate sequences for same store+type with same status
- **Solution**: Either activate existing sequence or deactivate old one first

### Slip number not incrementing
- **Check**: Is the sequence status "active"?
- **Check**: Are you in the same store as the sequence?
- **Check**: Are you using the same slip_type (receive/send)?

## Next Steps

After setup:
1. Configure sequences for each store and slip type combination
2. Test slip creation to verify auto-numbering
3. Train store manager on new workflow
4. Later: Integrate with Store Keeper dashboard for slip validation

## Support

For issues or customization requests, review:
- `app/Models/SlipSequence.php` - Model logic
- `app/Http/Controllers/SlipSequenceController.php` - Controller logic
- Database migration file for schema details
