<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$r = App\Models\RadiologyResult::find(7);
if (!$r) {
    echo "NOT_FOUND\n";
    exit(0);
}
echo "image_path:" . $r->image_path . "\n";
echo "image_paths:" . json_encode($r->image_paths) . "\n";
echo "image_urls:" . json_encode($r->image_urls) . "\n";
