<?php

// Force JSON for all API requests
if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api')) {
    if (!isset($_SERVER['HTTP_CONTENT_TYPE']) && !isset($_SERVER['CONTENT_TYPE'])) {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
    }
    if (!isset($_SERVER['HTTP_ACCEPT'])) {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
    }
}

// Handle malformed JSON from Postman
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $content = file_get_contents('php://input');
        
        if ($content) {
            // Fix encoding issues
            $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
            $content = str_replace(
                ["\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x98", "\xE2\x80\x99"],
                ['"', '"', "'", "'"],
                $content
            );
            
            // Test if it's valid JSON
            $decoded = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Invalid JSON - try to clean it up more
                $content = preg_replace('/[^\x20-\x7E\n\r\t{}\[\]":,]/', '', $content);
                $content = trim($content);
            }
        }
    }
}
