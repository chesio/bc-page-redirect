<?php

declare(strict_types=1);

namespace BlueChip\PageRedirect;

/**
 * Main plugin class
 */
class Plugin
{
    /**
     * Construct the plugin instance.
     *
     * @param string $plugin_filename Absolute path to main plugin file.
     */
    public function __construct(private string $plugin_filename)
    {}


    /**
     * Load the plugin by hooking into WordPress actions and filters.
     */
    public function load(): void
    {
        // Register initialization method.
        add_action('init', $this->init(...), 10, 0);
    }


    /**
     * Perform initialization tasks.
     * Method should be run (early) in init hook.
     *
     * @action https://developer.wordpress.org/reference/hooks/init/
     */
    private function init(): void
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
    public function uninstall(): void
    {
        // Clear any persistent data set by plugin.
        Persistence::deleteAll();
    }
}
