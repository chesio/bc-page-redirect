<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

abstract class Persistence
{
    /**
     * @var string Meta key that stores redirect type.
     */
    const REDIRECT_TYPE_META_KEY = 'bc-page-redirect/type';

    /**
     * @var string Meta key name that stores redirect value.
     */
    const REDIRECT_VALUE_META_KEY = 'bc-page-redirect/value';


    /**
     * Delete all persistent data stored by this plugin.
     *
     * @return bool True on success, false on error.
     */
    public static function deleteAll(): bool
    {
        $delete_types = delete_post_meta_by_key(self::REDIRECT_TYPE_META_KEY);
        $delete_values = delete_post_meta_by_key(self::REDIRECT_VALUE_META_KEY);

        return $delete_types && $delete_values;
    }


    /**
     * Get redirect type of given $post_id. Return empty string, if there is no redirect set.
     *
     * @param int $post_id
     * @return string
     */
    public static function getRedirectType(int $post_id): string
    {
        $redirect_type = get_post_meta($post_id, self::REDIRECT_TYPE_META_KEY, true);
        return ($redirect_type && Type::validate($redirect_type)) ? $redirect_type : '';
    }


    /**
     * Set $redirect_type for post with $post_id.
     *
     * @param int $post_id
     * @param string $redirect_type
     * @return bool True, if redirect type has been updated successfully or has not changed, false otherwise.
     */
    public static function setRedirectType(int $post_id, string $redirect_type): bool
    {
        if (!Type::validate($redirect_type)) {
            trigger_error("Invalid redirect type: $redirect_type", E_USER_WARNING);
            return false;
        }

        // We have to check against current value ourselves, because update_post_meta returns false not only when there
        // is an error, but also in case the new value is equal to the current one.
        return $redirect_type === self::getRedirectType($post_id)
            ? true
            : boolval(update_post_meta($post_id, self::REDIRECT_TYPE_META_KEY, $redirect_type));
    }


    /**
     * Get redirect value sanitized according to $redirect_type for post with $post_id. Return null, if $redirect_type
     * is invalid.
     *
     * @param int $post_id
     * @param string $redirect_type
     * @return mixed
     */
    public static function getRedirectValue(int $post_id, string $redirect_type)
    {
        return self::sanitizeValue(get_post_meta($post_id, self::REDIRECT_VALUE_META_KEY, true), $redirect_type);
    }


    /**
     * @param int $post_id
     * @param string $redirect_type
     * @param mixed $redirect_value
     * @return bool True, if redirect value has been updated successfully or has not changed, false otherwise.
     */
    public static function setRedirectValue(int $post_id, string $redirect_type, $redirect_value): bool
    {
        if (!Type::validate($redirect_type)) {
            trigger_error("Invalid redirect type: $redirect_type", E_USER_WARNING);
            return false;
        }

        $sanitized_redirect_value = self::sanitizeValue($redirect_value, $redirect_type);
        // We have to check against current value ourselves, because update_post_meta returns false not only when there
        // is an error, but also in case the new value is equal to the current one.
        return $sanitized_redirect_value === self::getRedirectValue($post_id, $redirect_type)
            ? true
            : boolval(update_post_meta($post_id, self::REDIRECT_VALUE_META_KEY, $sanitized_redirect_value));
    }


    /**
     * Sanitize redirect value according to given $redirect_type.
     *
     * @param mixed $redirect_value
     * @param string $redirect_type
     * @return mixed
     */
    private static function sanitizeValue($redirect_value, string $redirect_type)
    {
        switch ($redirect_type) {
            case Type::FIRST_SUBPAGE:
                return boolval($redirect_value);
            case Type::CUSTOM_PAGE:
                return intval($redirect_value);
            case Type::CUSTOM_URL:
                return $redirect_value;
            default:
                trigger_error("Invalid redirect type: $redirect_type", E_USER_WARNING);
                return null;
        }
    }
}
