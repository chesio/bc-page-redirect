<?php

declare(strict_types=1);

namespace BlueChip\PageRedirect;

/**
 * Simple factory that creates Redirect instance based on redirect ID.
 */
abstract class RedirectFactory
{
    /**
     * @var array Mapping between persistent ID and implementation class of every redirect type.
     */
    const MAPPING = [
        RedirectTypes\FirstSubpage::TYPE_ID => RedirectTypes\FirstSubpage::class,
        RedirectTypes\CustomPage::TYPE_ID => RedirectTypes\CustomPage::class,
        RedirectTypes\CustomUrl::TYPE_ID => RedirectTypes\CustomUrl::class,
    ];


    /**
     * @param int $post_id ID of post/page with the redirect.
     *
     * @return \BlueChip\PageRedirect\AbstractRedirect[]
     */
    public static function getAll(int $post_id): array
    {
        return array_map(fn (string $class): AbstractRedirect => new $class($post_id), self::MAPPING);
    }


    /**
     * Get redirect instance for given $type_id and $post_id.
     */
    public static function getRedirect(string $type_id, int $post_id): ?AbstractRedirect
    {
        $class = self::MAPPING[$type_id] ?? null;

        return $class ? (new $class($post_id)) : null;
    }
}
