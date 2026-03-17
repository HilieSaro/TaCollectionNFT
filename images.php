<?php
header('Content-Type: application/json');

$dir = __DIR__ . '/images';
$files = array_values(array_filter(scandir($dir), function($f) {
    return !in_array($f, ['.', '..']) && !is_dir($f);
}));

echo json_encode($files);