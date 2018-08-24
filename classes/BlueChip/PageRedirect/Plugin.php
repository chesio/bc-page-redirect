<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

/**
 * Main plugin class
 */
class Plugin
{
    /**
     * @var string Absolute path to main plugin file.
     */
    private $plugin_filename;


    /**
     * Construct the plugin instance.
     *
     * @param string $plugin_filename Absolute path to main plugin file.
     */
    public function __construct(string $plugin_filename)
    {
        $this->plugin_filename = $plugin_filename;
    }


    /**
     * Load the plugin by hooking into WordPress actions and filters.
     * Method should be invoked immediately on plugin load.
     */
    public function load()
    {
        // Register initialization method.
        add_action('init', [$this, 'init'], 10, 0);
    }


    /**
     * Perform initialization tasks.
     * Method should be run (early) in init hook.
     *
     * @action https://developer.wordpress.org/reference/hooks/init/
     */
    public function init()
    {
        if (is_admin()) {
            (new Backend())->init();
            (new MetaBox($this->plugin_filename))->init();
        } else {
            (new Frontend())->init();
        }
    }


    /**
     * Perform uninstallation tasks.
     * Method should be run on plugin uninstall.
     *
     * @link https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
     */
    public function uninstall()
    {
        // Clear any persistent data set by plugin.
        Persistence::deleteAll();
    }
}
