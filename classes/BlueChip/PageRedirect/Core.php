<?php

namespace BlueChip\PageRedirect;

/**
 * Plugin public API.
 */
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
        return is_object(Persistence::getRedirect($page_id));
    }


    /**
     * Return human-readable description of redirect type set to page with $page_id. If page has no redirect set,
     * empty string is returned.
     *
     * @param int $page_id
     * @return string
     */
    public static function getRedirectName(int $page_id): string
    {
        return is_object($redirect = Persistence::getRedirect($page_id)) ? $redirect->getShortName() : '';
    }


    /**
     * Return URL of redirect target of page with $page_id. If page has no redirect set, empty string is returned.
     *
     * @param int $page_id
     * @return string
     */
    public static function getRedirectLocation(int $page_id): string
    {
        return is_object($redirect = Persistence::getRedirect($page_id)) ? $redirect->getTargetLocation() : '';
    }
}
