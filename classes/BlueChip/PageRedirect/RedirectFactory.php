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
     * @return \BlueChip\PageRedirect\AbstractRedirect[]
     */
    public static function getAll(): array
    {
        return array_map(fn (string $class): AbstractRedirect => new $class(), self::MAPPING);
    }


    /**
     * Get redirect instance for given $type_id.
     */
    public static function getRedirect(string $type_id): ?AbstractRedirect
    {
        $class = self::MAPPING[$type_id] ?? null;

        return $class ? (new $class) : null;
    }
}
