<?php

/**
 * Compatibility file for footer.php
 * This file is kept for backward compatibility with existing pages
 * It redirects to the new main_layout.php
 */

// Get the buffered content from header.php
$pageContent = ob_get_clean();

// Include the main layout
include(dirname(__DIR__) . '/layouts/main_layout.php');
