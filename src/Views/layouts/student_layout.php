<?php

/**
 * Compatibility file for student_layout.php
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Đảm bảo chỉ cho student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Set page role and title
$pageRole = 'student';
$pageTitle = $title ?? 'Student Dashboard - PDU PMS';

// Start output buffering to capture content
ob_start();

// The content of each student page will be placed here

// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include(dirname(__DIR__) . '/layouts/main_layout.php');
