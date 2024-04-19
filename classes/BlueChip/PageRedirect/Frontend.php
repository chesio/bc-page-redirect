<?php

declare(strict_types=1);

namespace BlueChip\PageRedirect;

class Frontend
{
    /**
     * Initialize front-end integration.
     */
    public function init(): void
    {
        add_action('send_headers', $this->redirect(...), 10, 0);

        add_filter('wp_sitemaps_posts_query_args', $this->filterSitemapQueryArgs(...), 10, 1);
    }


    /**
     * Do a redirect for current request, if applicable, and exit.
     *
     * @hook https://developer.wordpress.org/reference/hooks/send_headers/
     */
    private function redirect()
    {
        // Redirect only works with pages.
        if (!is_page()) {
            return;
        }

        $location = Core::getRedirectLocation(get_the_ID());

        // Redirect needs to have a target location set.
        if ($location === '') {
            return;
        }

        if (wp_redirect($location, 301)) {
            exit;
        }
    }


    /**
     * Keep only pages with no redirect set in default XML sitemap.
     *
     * @hook https://developer.wordpress.org/reference/hooks/wp_sitemaps_posts_query_args/
     */
    private function filterSitemapQueryArgs(array $query_args): array
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
