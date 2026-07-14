<?php

$apiDir = __DIR__ . '/../app/Http/Controllers/Api/V1/Admin';
$webDir = __DIR__ . '/../app/Http/Controllers/Admin';

if (!is_dir($webDir)) {
    mkdir($webDir, 0755, true);
}

$files = glob($apiDir . '/*.php');

foreach ($files as $file) {
    $basename = basename($file);
    $content = file_get_contents($file);

    // Create Web Controller
    $webContent = $content;
    $webContent = str_replace('namespace App\Http\Controllers\Api\V1\Admin;', 'namespace App\Http\Controllers\Admin;', $webContent);
    $webContent = preg_replace('/use App\\\\Http\\\\Traits\\\\ApiResponse;\n/', '', $webContent);
    $webContent = preg_replace('/use ApiResponse;\n/', '', $webContent);
    
    // Remove if ($request->expectsJson()) { ... } block
    // Assuming non-nested brackets inside the expectsJson block for simplicity, which is true for our ApiResponses
    $webContent = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*\{[^}]+\}/s', '', $webContent);
    
    // Remove inline if ($request->expectsJson()) return ...;
    $webContent = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*return\s+[^;]+;/s', '', $webContent);

    // Some cleanup for empty lines
    $webContent = preg_replace("/\n\s*\n/", "\n\n", $webContent);

    file_put_contents($webDir . '/' . $basename, $webContent);

    // Create API Controller (strip out web returns)
    $apiContent = $content;
    
    // In API Controller, we want to REMOVE the fallback return view(...) or return back(...) or return redirect(...)
    // But it's tricky because the API return is inside the if ($request->expectsJson()).
    // Let's just remove the if ($request->expectsJson()) { and the closing } of that block, making it unconditional.
    $apiContent = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*\{\s*(return\s+[^}]+;)\s*\}/s', '$1', $apiContent);
    
    // For inline: if ($request->expectsJson()) return ...;
    $apiContent = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*(return\s+[^;]+;)/s', '$1', $apiContent);
    
    // Now remove the web fallbacks that come immediately after the (now unconditional) API return
    // e.g., return $this->success(...); \n return view(...);
    // Actually, any code after an unconditional return is dead code. But we can explicitly remove return view, return back, return redirect.
    $apiContent = preg_replace('/return\s+view\([^;]+;/s', '', $apiContent);
    $apiContent = preg_replace('/return\s+back\(\)[^;]*;/s', '', $apiContent);
    $apiContent = preg_replace('/return\s+redirect\([^;]+;/s', '', $apiContent);

    file_put_contents($apiDir . '/' . $basename, $apiContent);
}

echo "Done splitting controllers.\n";
