<?php

declare(strict_types=1);

namespace BlueChip\PageRedirect;

class Backend
{
    /**
     * Initialize back-end integration (except for meta box).
     * @see \BlueChip\Generic\PageRedirect\MetaBox
     */
    public function init(): void
    {
        add_action('load-post.php', $this->loadPost(...), 10, 0);
        add_filter('display_post_states', $this->displayPostStates(...), 20, 2);
        add_filter('page_link', $this->filterPageLink(...), 10, 2);
    }


    /**
     * In page list, mark a page if it redirects to another page/URL.
     *
     * @hook https://developer.wordpress.org/reference/hooks/display_post_states/
     */
    private function displayPostStates(array $post_states, \WP_Post $post): array
    {
        if (('page' === $post->post_type) && Core::hasRedirect($post->ID)) {
            $post_states['bc-page-redirect'] = Core::getRedirectName($post->ID);
        }
        return $post_states;
    }


    /**
     * Filter page link above the classic editor to display proper URL, if page redirects to another page/URL.
     *
     * @hook https://developer.wordpress.org/reference/hooks/page_link/
     */
    private function filterPageLink(string $link, int $post_id): string
    {
        return Core::getRedirectLocation($post_id) ?: $link;
    }


    /**
     * When editing page with a redirect, display a warning and disable content editor, if page has no content.
     *
     * @hook https://developer.wordpress.org/reference/hooks/load-pagenow/
     */
    private function loadPost(): void
    {
        // Get post ID.
        if (!empty($post_id = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT)) && Core::hasRedirect($post_id)) {
            // Display warning.
            add_action('edit_form_after_title', $this->printWarning(...), 10, 0);
            // Disable content editor, if page has no content.
            $post = get_post($post_id);
            if (empty($post->post_content)) {
                remove_post_type_support($post->post_type, 'editor');
            }
        }
    }


    /**
     * Display a warning about editing a page with a redirect.
     *
     * @hook https://developer.wordpress.org/reference/hooks/edit_form_after_title/
     */
    private function printWarning(): void
    {
        echo '<div class="notice notice-warning inline"><p>';
        echo esc_html__('You are currently editing a page that is set to redirect to another page or URL.', 'bc-page-redirect');
        echo '</p></div>';
    }
}
