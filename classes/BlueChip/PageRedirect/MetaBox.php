<?php

declare(strict_types=1);

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
    const REDIRECT_TYPE_FIELD_NAME = 'bc-page-redirect-type';


    /**
     * @param string $plugin_filename Absolute path to main plugin file.
     */
    public function __construct(private string $plugin_filename)
    {}


    /**
     * Initialize meta-box integration.
     */
    public function init(): void
    {
        // Init meta box in appropriate action
        add_action("add_meta_boxes_page", $this->addBox(...), 10, 1);
        // On each post save, check if we should save meta box data.
        add_action("save_post_page", $this->savePost(...), 10, 2);
        // On edit page load, enqueque JS assets etc.
        add_action('load-post.php', $this->loadPost(...), 10, 0);
        add_action('load-post-new.php', $this->loadPost(...), 10, 0);
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/add_meta_boxes_post_type/
     */
    private function addBox(): void
    {
        add_meta_box(
            'bc-page-redirect', // id
            __('Page redirect', 'bc-page-redirect'), // title
            $this->printBox(...), // callback
            'page', // screen
            'side' // context
        );
    }


    private function printBox(\WP_Post $post): void
    {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);

        $post_id = $post->ID;

        $redirects = RedirectFactory::getAll($post_id);
        $current_redirect = Persistence::getRedirect($post_id);
        $current_redirect_type_id = $current_redirect?->getTypeId() ?: '';

        ?>
        <div id="bc-page-redirect-meta-box">
            <p>
                <label for="bc-page-redirect-type"><?= esc_html('Redirect type:', 'bc-page-redirect'); ?></label><br />
                <select name="<?= self::REDIRECT_TYPE_FIELD_NAME; ?>" id="bc-page-redirect-type">
                    <option value=""><?= esc_html(__('No redirect', 'bc-page-redirect')); ?></option>
                    <?php foreach ($redirects as $type_id => $redirect) { ?>
                        <option value="<?= esc_attr($type_id); ?>" <?= selected($current_redirect_type_id, $type_id, false); ?>>
                            <?= esc_html($redirect->getShortName()); ?>
                        </option>
                    <?php } ?>
                </select>
            </p>

            <?php
                foreach ($redirects as $type_id => $redirect) {
                    echo '<div data-bc-page-redirect-type-data="' . esc_attr($type_id) . '">';
                    if ($current_redirect_type_id === $type_id) {
                        $current_redirect->printFormFields();
                    } else {
                        $redirect->printFormFields();
                    }
                    echo '</div>';
                }
            ?>
        </div>
        <?php
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/load-pagenow/
     */
    private function loadPost(): void
    {
        add_action('admin_enqueue_scripts', $this->enqueueScripts(...), 10, 0);
    }


    /**
     * @hook https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
     */
    private function enqueueScripts(): void
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
     */
    private function savePost(int $post_id, \WP_Post $post): void
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

        $redirect_type = filter_input(INPUT_POST, self::REDIRECT_TYPE_FIELD_NAME);

        if (($redirect = RedirectFactory::getRedirect($redirect_type, $post_id)) !== null) {
            $redirect->readFormInputData(INPUT_POST);
        }

        Persistence::setRedirect($post_id, $redirect);
    }
}
