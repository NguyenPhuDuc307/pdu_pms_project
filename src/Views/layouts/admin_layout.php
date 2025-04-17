<?php

/**
 * Compatibility file for admin_layout.php
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Đảm bảo chỉ cho admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Set page role and title
$pageRole = 'admin';
$pageTitle = $title ?? 'Admin Dashboard - PDU PMS';

// Start output buffering to capture content
ob_start();

// The content of each admin page will be placed here

// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include dirname(__DIR__) . '/layouts/main_layout.php';
