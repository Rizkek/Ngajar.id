<?php
require __DIR__ . '/vendor/autoload.php';
try {
    if (class_exists(\Illuminate\View\Compilers\BladeCompiler::class)) {
        echo "BladeCompiler class exists!\n";
    } else {
        echo "BladeCompiler class NOT found even after autoloading.\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
