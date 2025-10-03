<?php

/**
 * Router script for PHP's built-in development server
 * 
 * This file handles static file serving before passing requests to the application.
 * When using `./mason serve`, PHP's built-in server routes all requests through this file.
 */

// Get the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Check if the request is for a file that exists in the public directory
$filePath = __DIR__ . $uri;

// Serve static files directly if they exist
if ($uri !== '/' && file_exists($filePath) && is_file($filePath)) {
    // Let PHP's built-in server handle the file serving
    return false;
}

// Otherwise, pass to the application
require_once __DIR__ . '/index.php';

