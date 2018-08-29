<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect\RedirectTypes;

/**
 * Redirect to custom page.
 */
class CustomPage extends \BlueChip\PageRedirect\AbstractRedirect
{
    /**
     * @var string Persistent identifier of particular redirect type.
     */
    const TYPE_ID = 'custom-page';

    /**
     * @var string
     */
    const TARGET_PAGE_FIELD_NAME = 'bc-page-redirect-custom-page';


    /**
     * @return string Human-readable description of redirect type.
     */
    public function getShortName(): string
    {
        return __('Redirect to custom page', 'bc-page-redirect');
    }


    /**
     * @return string Redirect target location or empty string in case of invalid redirect data.
     */
    public function getTargetLocation(): string
    {
        return empty($this->data['page_id']) ? '' : (get_page_link($this->data['page_id']) ?: '');
    }


    /**
     * Sanitize redirect data - make sure correct keys are present and values have correct types.
     *
     * @param array $data Redirect data.
     * @return array Sanitized redirect data.
     */
    protected function sanitize(array $data): array
    {
        return [
            'page_id' => isset($data['page_id']) ? intval($data['page_id']) : 0,
        ];
    }


    /**
     * Output HTML snippet with form inputs to gather redirect data.
     */
    public function printFormFields(): void
    {
        // Grab list of all pages, index them by ID.
        $pages = self::indexPostsById(get_pages(['sort_column' => 'menu_order']));

        $selected_page_id = $this->data['page_id'] ?? 0;
        ?>
            <p>
                <label for="bc-page-redirect-custom-page"><?= esc_html('Page to redirect to:', 'bc-page-redirect'); ?></label><br />
                <select name="<?= self::TARGET_PAGE_FIELD_NAME; ?>" id="bc-page-redirect-custom-page">
                    <?php foreach ($pages as $page_id => $page) { ?>
                        <option value="<?= esc_attr($page_id); ?>" <?= selected($selected_page_id, $page_id, false); ?>>
                            <?= self::indent($page, $pages); ?> <?= esc_html($page->post_title); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>
        <?php
    }


    /**
     * Read and store redirect data from given input type (either INPUT_POST or INPUT_GET).
     *
     * @see filter_input()
     */
    public function readFormInputData(int $input_type): void
    {
        $this->data = [
            'page_id' => filter_input($input_type, self::TARGET_PAGE_FIELD_NAME, FILTER_VALIDATE_INT) ?: 0,
        ];
    }


    /**
     * @param array $non_indexed
     * @return array
     */
    private static function indexPostsById(array $non_indexed): array
    {
        $indexed = [];
        foreach ($non_indexed as $post) {
            $indexed[$post->ID] = $post;
        }
        return $indexed;
    }


    /**
     * @todo Recursive implementation is straight-forward and nice, but may be inefficient for site with a lot of pages.
     *
     * @param \WP_Post $page
     * @param array $pages
     * @param string $character
     * @return string
     */
    private static function indent(\WP_Post $page, array $pages, string $character = '-'): string
    {
        if (empty($parent_id = $page->post_parent) || !isset($pages[$parent_id])) {
            return '';
        }

        return $character . self::indent($pages[$parent_id], $pages, $character);
    }
}
