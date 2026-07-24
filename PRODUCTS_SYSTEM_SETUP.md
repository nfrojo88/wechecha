# Products/Materials Management System Setup Guide

## Overview
This document provides comprehensive instructions for setting up and using the Products/Materials Management System in the Construction ERP application. The system manages both **Consumable materials** (construction materials like cement, rebars, pipes) and **Fixed Assets** (equipment like generators, welding machines).

## Database Schema

### Products Table Structure

| Field Name | Type | Description |
|------------|------|-------------|
| id | Integer | Primary key, auto-increment |
| name | String(255) | Product/Material name |
| sku | String(100) | Unique stock keeping unit (MAT-XXXX format) |
| unit | String(50) | Unit of measurement (PCS, m, Kg, Roll, Gallon, etc.) |
| standard_length | Decimal(10,2) | Standard length dimension |
| standard_width | Decimal(10,3) | Standard width dimension |
| category | String(100) | "Consumable" or "Fixed Asset" |
| sub_category | String(100) | Additional categorization |
| max_stock | Decimal(10,2) | Maximum stock level |
| reorder_level | Integer | Minimum stock before reorder |
| carton_size | Integer | Items per carton/package |
| unit_price | Decimal(10,2) | Purchase/cost price |
| selling_price | Decimal(10,2) | Selling price |
| purchase_threshold | Decimal(5,2) | Purchase threshold percentage |
| equipment_condition | String(100) | Condition for fixed assets |
| assigned_to | String(255) | Who equipment is assigned to |
| current_location | String(255) | Physical location |
| asset_status | String(50) | Asset status (Available, In Use, etc.) |
| baseline_date | Date | Baseline date for tracking |
| created_at | Timestamp | Record creation timestamp |
| updated_at | Timestamp | Last update timestamp |
| deleted_at | Timestamp | Soft delete timestamp |

### Key Indexes
- `sku` - Unique index
- `category` - Regular index for filtering
- `asset_status` - Regular index for status queries
- `current_location` - Regular index for location-based queries

## Installation Steps

### Step 1: Run Migration

```bash
php artisan migrate
```

This will create the `products` table with all necessary fields and indexes.

### Step 2: Run Seeder (Optional)

The seeder contains 30 representative sample products covering various categories:

```bash
php artisan db:seed --class=ProductSeeder
```

Or include in DatabaseSeeder:

```php
// database/seeders/DatabaseSeeder.php
public function run()
{
    $this->call([
        ProductSeeder::class,
    ]);
}
```

Then run:
```bash
php artisan db:seed
```

## Product Categories

### 1. Consumable Materials
Materials that are used up during construction:

**Examples:**
- **Construction Materials**: Cement, Sand, Aggregate, Brick, HCB
- **Steel & Metal**: Rebars (Ø8mm to Ø24mm), Angle Iron, RHS, CHS
- **Pipes & Fittings**: PVC, PPR, HDPE pipes and fittings
- **Electrical**: Wires, Cables, Breakers, Switches, Sockets, LED lights
- **Finishing Materials**: Ceramic tiles, Porcelain, Paint, Gypsum
- **Plumbing**: WC, Hand Wash Basin (HWB), Mixers, Valves
- **Hardware**: Nails, Screws, Bolts, Fisher, Electrodes

### 2. Fixed Assets
Equipment and tools with longer lifespan:

**Examples:**
- **Heavy Equipment**: Generators, Excavator, Concrete Mixer, Concrete Vibrator
- **Vehicles**: Sino Truck, ISUZU, Pick-Up, Automobile
- **Power Tools**: Welding Machine, Grinder, Drill Machine, Cutter Machine
- **Construction Equipment**: Scaffolding, Gong Lift, Winch, Compactor
- **Measuring Tools**: Laser Level, Digital Caliper, Spirit Level
- **Office Equipment**: Computer, Printer, Attendance Machine

## Common Units of Measurement

| Unit | Description | Common Items |
|------|-------------|--------------|
| PCS | Pieces | Tiles, Switches, Equipment |
| m | Meter | Pipes, Cables, Rebars |
| M2 | Square Meter | Tiles, Ceramic, Grass |
| m3 | Cubic Meter | Concrete, Sand, Aggregate |
| Kg | Kilogram | Wire, Metal, Paint additives |
| Berga | Berga (12m length) | Rebars |
| Roll | Roll | Cables, Conduits |
| Gallon | Gallon | Paint, Chemical |
| Liter | Liter | Diesel, Benzele, Water |
| Quintal | Quintal | Cement |
| Packet | Packet | Nails, Screws |
| Cartoon | Carton | Nails, Skirting |

## Model Usage Examples

### Basic CRUD Operations

#### Create a Product

```php
use App\Models\Product;

$product = Product::create([
    'name' => 'Cement',
    'sku' => 'MAT-0042',
    'unit' => 'Quintal',
    'category' => 'Consumable',
    'unit_price' => 2100.00,
    'selling_price' => 2300.00,
    'max_stock' => 100.00,
    'reorder_level' => 25,
]);
```

#### Update a Product

```php
$product = Product::find(1);
$product->update([
    'unit_price' => 2200.00,
    'max_stock' => 120.00,
]);
```

#### Soft Delete

```php
$product = Product::find(1);
$product->delete(); // Soft delete

// Restore
$product->restore();

// Permanent delete
$product->forceDelete();
```

### Using Scopes

#### Get Consumable Products

```php
$consumables = Product::consumables()->get();
```

#### Get Fixed Assets

```php
$fixedAssets = Product::fixedAssets()->get();
```

#### Get Available Products

```php
$available = Product::available()->get();
```

#### Get Low Stock Items

```php
$lowStock = Product::lowStock()->get();
```

#### Combined Scopes

```php
// Get available consumables with low stock
$items = Product::consumables()
    ->available()
    ->lowStock()
    ->get();
```

### Using Helper Methods

```php
$product = Product::find(1);

// Check product type
if ($product->isConsumable()) {
    // Handle consumable logic
}

if ($product->isFixedAsset()) {
    // Handle fixed asset logic
}

// Check stock level
if ($product->isLowStock()) {
    // Trigger reorder alert
}

// Check availability
if ($product->isAvailable()) {
    // Product can be issued
}
```

### Using Accessors

```php
$product = Product::find(1);

// Get profit margin percentage
$margin = $product->profit_margin; // Returns calculated percentage

// Get formatted prices
$unitPrice = $product->formatted_unit_price; // "2,100.00 ETB"
$sellingPrice = $product->formatted_selling_price; // "2,300.00 ETB"
```

## Controller Example

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category'])
            ->orderBy('name')
            ->paginate(50);
            
        return view('products.index', compact('products'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'unit' => 'required|string|max:50',
            'category' => 'required|in:Consumable,Fixed Asset',
            'unit_price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'max_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);
        
        $product = Product::create($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }
    
    public function lowStockReport()
    {
        $lowStockItems = Product::lowStock()
            ->consumables()
            ->orderBy('category')
            ->get();
            
        return view('products.low-stock-report', compact('lowStockItems'));
    }
}
```

## SKU Format

The system uses a standardized SKU format:
- Format: `MAT-XXXX`
- Example: `MAT-0001`, `MAT-0042`, `MAT-1228`
- Sequential numbering with 4-digit zero-padding
- Currently ranges from MAT-0001 to MAT-1228

## Asset Status Values

For Fixed Assets, the following status values are used:
- **Available**: Ready for use
- **In Use**: Currently assigned and being used
- **Under Maintenance**: Being serviced or repaired
- **Damaged**: Not functional, requires repair

## Best Practices

1. **Always use SKU**: Ensure every product has a unique SKU
2. **Set reorder levels**: Configure appropriate reorder levels for consumables
3. **Track fixed assets**: Maintain accurate location and assignment data for equipment
4. **Use soft deletes**: Never permanently delete products with transaction history
5. **Validate prices**: Ensure unit_price and selling_price are set correctly
6. **Update stock levels**: Keep max_stock updated based on inventory counts
7. **Use categories consistently**: Stick to "Consumable" or "Fixed Asset" exactly

## Troubleshooting

### Migration Issues

If migration fails, check:
- Database connection in `.env`
- Sufficient permissions
- No existing table conflicts

### Seeder Issues

If seeder fails:
```bash
php artisan db:seed --class=ProductSeeder --force
```

### SKU Conflicts

If you get unique constraint errors:
- Check for duplicate SKUs in your data
- Update SKU before creating new products

## Next Steps

1. Create product categories management
2. Add inventory tracking (stock in/out)
3. Implement purchase order system
4. Add supplier management
5. Create stock movement reports
6. Implement barcode/QR code generation for SKUs
7. Add image uploads for products
8. Create mobile app for inventory scanning

## Support

For issues or questions, contact your system administrator or refer to the main ERP documentation.

---

**Document Version**: 1.0  
**Last Updated**: 2026-07-08  
**Created By**: System Administrator
