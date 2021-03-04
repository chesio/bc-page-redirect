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

        add_filter('wp_sitemaps_posts_query_args', [$this, 'filterSitemapQueryArgs'], 10, 1);
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


    /**
     * Keep only pages with no redirect set in default XML sitemap.
     *
     * @hook https://developer.wordpress.org/reference/hooks/wp_sitemaps_posts_query_args/
     */
    public function filterSitemapQueryArgs(array $query_args): array
    {
        if ($query_args['post_type'] === 'page') {
            // See: https://developer.wordpress.org/reference/classes/WP_Query/#custom-field-post-meta-parameters
            if (!isset($query_args['meta_query'])) {
                $query_args['meta_query'] = [];
            }

            $query_args['meta_query'][] = [
                'key' => \BlueChip\PageRedirect\Persistence::REDIRECT_META_KEY,
                'compare' => 'NOT EXISTS',
            ];
        }

        return $query_args;
    }
}
