<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

abstract class Core
{
    /**
     * Return true, if page with $page_id has a redirect set, false otherwise.
     *
     * @param int $page_id
     * @return bool
     */
    public static function hasRedirect(int $page_id): bool
    {
        return !empty(Persistence::getRedirectType($page_id));
    }


    /**
     * Return human-readable description of redirect type set to page with $page_id. If page has no redirect set, empty
     * string is returned.
     *
     * @param int $page_id
     * @return string
     */
    public static function getRedirectLabel(int $page_id): string
    {
        return Type::translate(Persistence::getRedirectType($page_id));
    }


    /**
     * Return URL of redirect target of page with $page_id. If page has no redirect set, empty string is returned.
     *
     * @param int $page_id
     * @return string
     */
    public static function getRedirectLocation(int $page_id): string
    {
        switch (Persistence::getRedirectType($page_id)) {
            case Type::FIRST_SUBPAGE:
                return self::getFirstSubpageUrl($page_id);
            case Type::CUSTOM_PAGE:
                return self::getCustomPageUrl($page_id);
            case Type::CUSTOM_URL:
                return self::getCustomUrl($page_id);
            default:
                return '';
        }
    }


    /**
     * Return URL of the first subpage of page with $page_id.
     *
     * @param int $page_id
     * @return string
     */
    private static function getFirstSubpageUrl(int $page_id): string
    {
        $pages = get_pages([
            'parent' => $page_id,
            'sort_column' => 'menu_order',
            'number' => 1,
        ]);

        return empty($pages) ? '' : (get_page_link($pages[0]->ID) ?: '');
    }


    /**
     * Return URL of page set as redirect target of page with $page_id.
     *
     * @param int $page_id
     * @return string
     */
    private static function getCustomPageUrl(int $page_id): string
    {
        $target_page_id = Persistence::getRedirectValue($page_id, Type::CUSTOM_PAGE);

        return empty($target_page_id) ? '' : (get_page_link($target_page_id) ?: '');
    }


    /**
     * Return URL set as redirect target of page with $page_id.
     *
     * @param int $page_id
     * @return string
     */
    private static function getCustomUrl(int $page_id): string
    {
        return Persistence::getRedirectValue($page_id, Type::CUSTOM_URL) ?: '';
    }
}
