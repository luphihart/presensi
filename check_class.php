<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = App\Models\Student::with(['user', 'classRoom'])
    ->where('is_active', true)
    ->orderByDesc('monthly_points')
    ->take(5)
    ->get();

foreach ($students as $st) {
    echo "Name: " . ($st->user->name ?? 'N/A') . "\n";
    echo "  class_room_id: " . ($st->class_room_id ?? 'NULL') . "\n";
    echo "  classRoom (name): " . ($st->classRoom ? $st->classRoom->name : 'NULL (no relation)') . "\n";
    echo "  classRoom (major): " . ($st->classRoom ? ($st->classRoom->major ?? 'NULL') : 'NULL') . "\n";
    echo "  classRoom (full_name): " . ($st->classRoom ? $st->classRoom->full_name : 'NULL') . "\n";
    echo "\n";
}
