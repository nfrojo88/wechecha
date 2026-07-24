<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "material_request_items: " . json_encode(Illuminate\Support\Facades\Schema::getColumnListing('material_request_items')) . "\n";
