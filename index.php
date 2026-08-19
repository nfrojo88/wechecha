<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 */

if (isset($_SERVER['REQUEST_URI']) && (str_contains($_SERVER['REQUEST_URI'], 'index.php') || str_starts_with($_SERVER['REQUEST_URI'], '/public'))) {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = preg_replace('#^/(public/)?(index\.php/?)?#i', '/', $uri);
    if (empty($uri) || $uri[0] !== '/') {
        $uri = '/' . $uri;
    }
    $_SERVER['REQUEST_URI'] = $uri;
}

require_once __DIR__.'/public/index.php';
