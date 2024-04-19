<?php

/**
 * Perform plugin uninstall.
 */

// If file is not invoked by WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Register autoloader for this plugin.
require_once __DIR__ . '/autoload.php';

// Construct plugin instance and run uninstall actions.
(new \BlueChip\PageRedirect\Plugin(__DIR__ . '/bc-page-redirect.php'))->uninstall();
