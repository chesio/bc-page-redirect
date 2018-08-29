<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect\RedirectTypes;

class FirstSubpage extends \BlueChip\PageRedirect\AbstractRedirect
{
    /**
     * @var string Persistent identifier of particular redirect type.
     */
    const TYPE_ID = 'first-subpage';

    /**
     * @var string
     */
    const POST_ID_FIELD_NAME = 'post_ID';


    /**
     * @return string Human-readable description of redirect type.
     */
    public function getShortName(): string
    {
        return __('Redirect to first subpage', 'bc-page-redirect');
    }


    /**
     * @return string Redirect target location or empty string in case of invalid redirect data.
     */
    public function getTargetLocation(): string
    {
        $pages = get_pages([
            'parent' => $this->data['page_id'],
            'sort_column' => 'menu_order',
            'number' => 1,
        ]);

        return empty($pages) ? '' : (get_page_link($pages[0]->ID) ?: '');
    }


    /**
     * Sanitize redirect data - make sure correct keys are present and values have correct types.
     *
     * @param array $data Redirect data.
     * @return array Sanitized redirect data.
     */
    protected function sanitize(array $data): array
    {
        return [
            'page_id' => isset($data['page_id']) ? intval($data['page_id']) : 0,
        ];
    }


    /**
     * Output HTML snippet with form inputs to gather redirect data.
     */
    public function printFormFields(): void
    {
        // No extra fields necessary for this redirect type.
    }


    /**
     * Read and store redirect data from given input type (either INPUT_POST or INPUT_GET).
     *
     * @see filter_input()
     */
    public function readFormInputData(int $input_type): void
    {
        $this->data = [
            'page_id' => filter_input($input_type, 'post_ID', FILTER_VALIDATE_INT) ?: 0,
        ];
    }
}
