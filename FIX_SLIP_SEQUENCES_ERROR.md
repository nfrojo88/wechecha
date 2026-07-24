# Fix: Route [slip-sequences.create] not defined

## Problem
You're seeing this error:
```
Symfony\Component\Routing\Exception\RouteNotFoundException
Route [slip-sequences.create] not defined.
```

## Root Cause
Laravel's route cache is outdated. All routes and views are correctly configured with the `store-manager.` prefix, but Laravel is still serving cached routes.

## Solution
Run these commands on your production server:

```bash
cd /data/var/www/vhosts/wechechaconstruction.et/httpdocs
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan optimize
```

If you don't have SSH access, ask your hosting provider to run these commands.

## Alternative: Run via Web Browser
If you can't access SSH, create a temporary file in your `public/` directory:

**public/clear-cache.php**
```php
<?php
// Run this file once, then DELETE IT immediately after

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Clear all caches
$kernel->call('route:clear');
$kernel->call('cache:clear');
$kernel->call('view:clear');
$kernel->call('config:clear');
$kernel->call('optimize');

echo "All caches cleared! DELETE THIS FILE NOW for security.";
```

Then visit: `https://www.wechechaconstruction.et/clear-cache.php`

**IMPORTANT:** Delete `clear-cache.php` immediately after running it!

## Verification
After clearing caches, the following routes should work:
- `/store-manager/slip-sequences` - List all sequences
- `/store-manager/slip-sequences/create` - Create new sequence
- `/store-manager/slip-sequences/{id}/edit` - Edit sequence

## What Was Fixed
All routes and views already use the correct naming:
- ✅ `routes/web.php` - Routes defined inside `store-manager` group
- ✅ `resources/views/slip-sequences/*.blade.php` - All use `store-manager.slip-sequences.*`
- ✅ `app/Http/Controllers/SlipSequenceController.php` - Redirects use full route names
- ✅ `resources/views/layouts/sidebar.blade.php` - Menu link correct

The issue is ONLY the cached routes on the server.
