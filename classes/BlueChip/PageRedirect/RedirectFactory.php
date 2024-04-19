<?php

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
        return array_map(
            function (string $class): AbstractRedirect {
                return new $class();
            },
            self::MAPPING
        );
    }


    /**
     * @param string $type
     * @return null|\BlueChip\PageRedirect\AbstractRedirect
     */
    public static function getRedirect(string $type): ?AbstractRedirect
    {
        if (isset(self::MAPPING[$type])) {
            $class = self::MAPPING[$type];
            return new $class;
        } else {
            return null;
        }
    }
}
