<?php

/**
 * Compatibility file for header.php
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Set page role based on session if available
$pageRole = isset($_SESSION['user_id']) && isset($_SESSION['role']) ? $_SESSION['role'] : 'public';

// Set page title
$pageTitle = $title ?? 'PDU - PMS';

// Start output buffering to capture content
ob_start();

// The content of each page will be placed here after this include
