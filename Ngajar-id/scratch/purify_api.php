<?php

$apiDir = __DIR__ . '/../app/Http/Controllers/Api/V1';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($apiDir));

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    if ($file->getExtension() !== 'php') continue;

    $path = $file->getPathname();
    $content = file_get_contents($path);

    // Skip if it doesn't have expectsJson and doesn't have redirect
    if (!str_contains($content, 'expectsJson') && !str_contains($content, 'redirect')) {
        continue;
    }

    $originalContent = $content;

    // 1. Remove if ($request->expectsJson()) { ... } block
    // We want to keep the inside of the block and discard the if and braces.
    // e.g. if ($request->expectsJson()) { return $this->success(...); }
    // replaced with: return $this->success(...);
    $content = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*\{\s*(return\s+[^}]+;)\s*\}/s', '$1', $content);
    
    // 2. Remove inline if ($request->expectsJson()) return ...;
    // e.g. if ($request->expectsJson()) return $this->success(...);
    // replaced with: return $this->success(...);
    $content = preg_replace('/if\s*\(\$request->expectsJson\(\)\)\s*(return\s+[^;]+;)/s', '$1', $content);

    // 3. Remove the web fallbacks that come immediately after the (now unconditional) API return
    $content = preg_replace('/return\s+view\([^;]+;/s', '', $content);
    $content = preg_replace('/return\s+back\(\)[^;]*;/s', '', $content);
    $content = preg_replace('/return\s+redirect\([^;]+;/s', '', $content);

    // 4. Sometimes it's just an else block for the web fallback
    // e.g. } else { return redirect(...); }
    // Since we stripped the if block, there might be leftover '} else {' or similar if our regex missed it.
    // Actually, our regex only matches the exact block if it is closed by }.
    
    // 5. Cleanup empty lines
    $content = preg_replace("/\n\s*\n/", "\n\n", $content);

    if ($originalContent !== $content) {
        file_put_contents($path, $content);
        echo "Purified: " . $file->getFilename() . "\n";
    }
}

echo "Done purifying API controllers.\n";
