<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

class Frontend
{
    /**
     * Initialize front-end integration.
     */
    public function init()
    {
        add_action('template_redirect', [$this, 'redirect']);
    }


    /**
     * Do a redirect for current request, if applicable, and exit.
     * @hook https://developer.wordpress.org/reference/hooks/template_redirect/
     */
    public function redirect()
    {
        // Redirect only works with pages.
        if (is_page() && !empty($location = Core::getRedirectLocation(get_the_ID())) && wp_redirect($location, 301)) {
            exit;
        }
    }
}
