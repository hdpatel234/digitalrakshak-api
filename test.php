<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $c = \App\Models\Candidate::with(['packages', 'candidateServices.service', 'candidateInvitations'])->first();
    echo "Success: " . ($c ? "Found" : "Not Found") . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
