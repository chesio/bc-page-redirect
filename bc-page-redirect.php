<?php
/**
 * Plugin Name: BC Page Redirect
 * Plugin URI: https://github.com/chesio/bc-page-redirect
 * Description: Let a page redirect to its first subpage, any other page or external URL.
 * Version: 2.2.0-dev
 * Author: Česlav Przywara <ceslav@przywara.cz>
 * Author URI: https://www.chesio.com
 * Requires PHP: 8.1
 * Requires WP: 4.9
 * Tested up to: 5.6
 * Text Domain: bc-page-redirect
 * GitHub Plugin URI: https://github.com/chesio/bc-page-redirect
 */

if (version_compare(PHP_VERSION, '8.1', '<')) {
    // Warn user that his/her PHP version is too low for this plugin to function.
    add_action('admin_notices', function () {
        echo '<div class="error"><p>';
        echo esc_html(
            sprintf(
                __('BC Page Redirect plugin requires PHP 8.1 to function properly, but you have version %s installed. The plugin has been auto-deactivated.', 'bc-page-redirect'),
                PHP_VERSION
            )
        );
        echo '</p></div>';
        // https://make.wordpress.org/plugins/2015/06/05/policy-on-php-versions/
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }, 10, 0);

    // Self deactivate.
    add_action('admin_init', function () {
        deactivate_plugins(plugin_basename(__FILE__));
    }, 10, 0);

    // Bail.
    return;
}


// Register autoloader for this plugin.
require_once __DIR__ . '/autoload.php';

// Construct plugin instance.
$bc_page_redirect = new \BlueChip\PageRedirect\Plugin(__FILE__);

// Load the plugin.
$bc_page_redirect->load();
