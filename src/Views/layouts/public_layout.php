<?php
/**
 * Compatibility file for public pages
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Set page role and title
$pageRole = 'public';
$pageTitle = $title ?? 'PDU - PMS';

// Start output buffering to capture content
ob_start();

// The content of each public page will be placed here

// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include(dirname(__DIR__) . '/layouts/main_layout.php');
?>
