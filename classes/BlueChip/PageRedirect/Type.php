<?php
/**
 * @package BC_Page_Redirect
 */
namespace BlueChip\PageRedirect;

abstract class Type
{
    /**
     * @var string ID of "redirect to first subpage".
     */
    const FIRST_SUBPAGE = 'first-subpage';

    /**
     * @var string ID of "redirect to custom page".
     */
    const CUSTOM_PAGE = 'custom-page';

    /**
     * @var string ID of "redirect to custom URL".
     */
    const CUSTOM_URL = 'custom-url';


    /**
     * Return list of all redirect types, optionally with human-readable descriptions.
     *
     * @param bool $describe If true, list values are human-readable descriptions.
     * @return array List of all redirect types.
     */
    public static function getAll(bool $describe = false): array
    {
        $reflection = new \ReflectionClass(self::class);
        $types = $reflection->getConstants();

        return $describe
            ? array_map(
                [self::class, 'translate'],
                array_combine($types, $types)
            )
            : $types
        ;
    }


    /**
     * Return human-readable description of redirect $type. If $type is invalid, empty string is returned.
     *
     * @param string $type
     * @return string
     */
    public static function translate(string $type): string
    {
        switch ($type) {
            case self::FIRST_SUBPAGE:
                return __('Redirect to first subpage', 'bc-page-redirect');
            case self::CUSTOM_PAGE:
                return __('Redirect to custom page', 'bc-page-redirect');
            case self::CUSTOM_URL:
                return __('Redirect to custom URL', 'bc-page-redirect');
            default:
                return '';
        }
    }


    /**
     * Return true, if $type is a valid redirect type, false otherwise.
     *
     * @param string $type
     * @return bool
     */
    public static function validate(string $type): bool
    {
        return in_array($type, self::getAll(), true);
    }
}
