<?php
/**
 * Perform plugin uninstall.
 *
 * @package BC_Page_Redirect
 */

// If file is not invoked by WordPress, exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Register autoloader for this plugin.
require_once __DIR__ . '/autoload.php';

// Construct plugin instance.
$bc_page_redirect = new \BlueChip\PageRedirect\Plugin(__DIR__ . '/bc-page-redirect.php');
// Run uninstall actions.
$bc_page_redirect->uninstall();
