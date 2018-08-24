<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

class MetaBox
{
    /**
     * @var string
     */
    const NONCE_ACTION = 'bc-page-redirect-save';

    /**
     * @var string
     */
    const NONCE_NAME = 'bc-page-redirect-nonce';

    /**
     * @var string
     */
    const REDIRECT_TYPE_FIELD_ID = 'bc-page-redirect-type';

    /**
     * @var string
     */
    const REDIRECT_TYPE_FIELD_NAME = 'bc-page-redirect-type';

    /**
     * @var string
     */
    const REDIRECT_VALUE_FIELD_NAME = 'bc-page-redirect-value';


    /**
     * @var string Absolute path to main plugin file.
     */
    private $plugin_filename;


    /**
     * @param string $plugin_filename Absolute path to main plugin file.
     */
    public function __construct(string $plugin_filename)
    {
        $this->plugin_filename = $plugin_filename;
    }


    /**
     * Initialize meta-box integration.
     */
    public function init()
    {
        // Init meta box in appropriate action
        add_action("add_meta_boxes_page", [$this, 'addBox']);
        // On each post save, check if we should save meta box data.
        add_action("save_post_page", [$this, 'savePost'], 10, 2);
        // On edit page load, enqueque JS assets etc.
        add_action('load-post.php', [$this, 'loadPost'], 10, 0);
        add_action('load-post-new.php', [$this, 'loadPost'], 10, 0);
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/add_meta_boxes_post_type/
     */
    public function addBox()
    {
        add_meta_box(
            'bc-page-redirect', // id
            __('Page redirect', 'bc-page-redirect'), // title
            [$this, 'printBox'], // callback
            'page', // screen
            'side' // context
        );
    }


    /**
     * @param \WP_Post $post
     */
    public function printBox(\WP_Post $post)
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        // Grab list of all pages, index them by ID.
        $pages = self::indexPostsById(get_pages(['sort_column' => 'menu_order']));

        $redirect_type = Persistence::getRedirectType($post->ID);
        $redirect_value = Persistence::getRedirectValue($post->ID, $redirect_type);

        $custom_page_id = $redirect_type === Type::CUSTOM_PAGE ? $redirect_value : 0;
        $custom_url = $redirect_type === Type::CUSTOM_URL ? $redirect_value : '';

        ?>
        <div id="bc-page-redirect-meta-box">
            <p>
                <label for="bc-page-redirect-type"><?= esc_html('Redirect type:', 'bc-page-redirect'); ?></label><br />
                <select name="<?= self::REDIRECT_TYPE_FIELD_NAME; ?>" id="bc-page-redirect-type">
                    <option value=""><?= esc_html('No redirect', 'bc-page-redirect'); ?></option>
                    <?php foreach (Type::getAll(true) as $type => $label) { ?>
                        <option value="<?= esc_attr($type); ?>" <?= selected($redirect_type, $type, false); ?>>
                            <?= esc_html($label); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>

            <p class="js-bc-redirect-value">
                <label for="bc-page-redirect-custom-page"><?= esc_html('Page to redirect to:', 'bc-page-redirect'); ?></label><br />
                <select name="<?= self::REDIRECT_VALUE_FIELD_NAME; ?>" id="bc-page-redirect-custom-page" <?= $redirect_type === Type::CUSTOM_PAGE ? '' : 'disabled="disabled"'; ?> data-bc-redirect-value-for-type="<?= esc_attr(Type::CUSTOM_PAGE); ?>">
                    <?php foreach ($pages as $page_id => $page) { ?>
                        <option value="<?= esc_attr($page_id); ?>" <?= selected($custom_page_id, $page_id, false); ?>>
                            <?= self::indent($page, $pages); ?> <?= esc_html($page->post_title); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>

            <p class="js-bc-redirect-value">
                <label for="bc-page-redirect-custom-url"><?= esc_html('URL to redirect to:', 'bc-page-redirect'); ?></label><br />
                <input type="text" name="<?= self::REDIRECT_VALUE_FIELD_NAME; ?>" id="bc-page-redirect-custom-url" value="<?= esc_attr($custom_url); ?>" <?= $redirect_type === Type::CUSTOM_URL ? '' : 'disabled="disabled"'; ?> data-bc-redirect-value-for-type="<?= esc_attr(Type::CUSTOM_URL); ?>" style="width: 20em; max-width: 100%;" />
            </p>

        </div>
        <?php
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/load-pagenow/
     */
    public function loadPost()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts'], 10, 0);
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     */
    public function enqueueScripts()
    {
        $script_handle = 'bc-page-redirect-meta-box';
        $script_path = 'assets/js/page-redirect-meta-box.js';

        wp_enqueue_script(
            $script_handle,
            plugin_dir_url($this->plugin_filename) . $script_path,
            ['jquery'],
            filemtime(plugin_dir_path($this->plugin_filename) . $script_path),
            true
        );
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/save_post_post-post_type/
     *
     * @param int $post_id
     * @param \WP_Post $post
     */
    public function savePost(int $post_id, \WP_Post $post)
    {
        // Don't save meta boxes for revisions or autosaves.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
        if (filter_input(INPUT_POST, 'post_ID', FILTER_VALIDATE_INT) !== $post_id) {
            return;
        }

        // Check the nonce.
        if (!wp_verify_nonce(filter_input(INPUT_POST, self::NONCE_NAME), self::NONCE_ACTION)) {
            return;
        }

        // Check user has permission to edit
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        // Save redirect type, bail on failure.
        $redirect_type = filter_input(INPUT_POST, self::REDIRECT_TYPE_FIELD_NAME, FILTER_SANITIZE_STRING);

        if (!Persistence::setRedirectType($post_id, $redirect_type)) {
            return;
        }

        // Save redirect value.
        switch ($redirect_type) {
            case Type::FIRST_SUBPAGE:
                $redirect_value = true;
                break;
            case Type::CUSTOM_PAGE:
                $redirect_value = filter_input(INPUT_POST, self::REDIRECT_VALUE_FIELD_NAME, FILTER_VALIDATE_INT);
                break;
            case Type::CUSTOM_URL:
                $redirect_value = filter_input(INPUT_POST, self::REDIRECT_VALUE_FIELD_NAME, FILTER_VALIDATE_URL);
                break;
            default:
                $redirect_value = null;
        }

        Persistence::setRedirectValue($post_id, $redirect_type, $redirect_value);
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
