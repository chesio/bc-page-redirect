<?php

namespace BlueChip\PageRedirect;

/**
 * Wrapper that performs reads/writes of plugin persistent data.
 */
abstract class Persistence
{
    /**
     * @var string Meta key that stores redirect data.
     */
    const REDIRECT_META_KEY = 'bc-page-redirect';


    /**
     * Delete all persistent data stored by this plugin.
     *
     * @return bool True on success, false on error.
     */
    public static function deleteAll(): bool
    {
        return delete_post_meta_by_key(self::REDIRECT_META_KEY);
    }


    /**
     * Get redirect information for given post.
     *
     * @param int $post_id
     * @return null|\BlueChip\PageRedirect\AbstractRedirect
     */
    public static function getRedirect(int $post_id): ?AbstractRedirect
    {
        $raw_redirect = get_post_meta($post_id, self::REDIRECT_META_KEY, true);

        $redirect_type = isset($raw_redirect['type']) && is_string($raw_redirect['type']) ? $raw_redirect['type'] : '';

        if (is_object($redirect = RedirectFactory::getRedirect($redirect_type))) {
            $redirect_data = isset($raw_redirect['data']) && is_array($raw_redirect['data']) ? $raw_redirect['data'] : [];
            $redirect->setData($redirect_data);
        }

        return $redirect;
    }


    /**
     * Store redirect information for given post.
     *
     * @param int $post_id
     * @param null|\BlueChip\PageRedirect\AbstractRedirect $redirect
     */
    public static function setRedirect(int $post_id, ?AbstractRedirect $redirect): void
    {
        if (is_object($redirect)) {
            update_post_meta($post_id, self::REDIRECT_META_KEY, ['type' => $redirect->getTypeId(), 'data' => $redirect->getData()]);
        } else {
            delete_post_meta($post_id, self::REDIRECT_META_KEY);
        }
    }
}
