<?php

/**
 * Compatibility file for teacher_layout.php
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Đảm bảo chỉ cho teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /pdu_pms_project/public/login');
    exit;
}

// Set page role and title
$pageRole = 'teacher';
$pageTitle = $title ?? 'Teacher Dashboard - PDU PMS';

// Start output buffering to capture content
ob_start();

// The content of each teacher page will be placed here

// Get the buffered content
$pageContent = ob_get_clean();

// Include the main layout
include(dirname(__DIR__) . '/layouts/main_layout.php');
