<?php

declare(strict_types=1);

namespace BlueChip\PageRedirect;

/**
 * Plugin public API.
 */
abstract class Core
{
    /**
     * Return true, if page with $page_id has a redirect set, false otherwise.
     */
    public static function hasRedirect(int $page_id): bool
    {
        return Persistence::getRedirect($page_id) !== null;
    }


    /**
     * Return human-readable description of redirect type set to page with $page_id. If page has no redirect set,
     * empty string is returned.
     */
    public static function getRedirectName(int $page_id): string
    {
        return Persistence::getRedirect($page_id)?->getShortName() ?: '';
    }


    /**
     * Return URL of redirect target of page with $page_id. If page has no redirect set, empty string is returned.
     */
    public static function getRedirectLocation(int $page_id): string
    {
        return Persistence::getRedirect($page_id)?->getTargetLocation() ?: '';
    }
}
