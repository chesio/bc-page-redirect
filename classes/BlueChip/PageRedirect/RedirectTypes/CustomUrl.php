<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect\RedirectTypes;

class CustomUrl extends \BlueChip\PageRedirect\AbstractRedirect
{
    /**
     * @var string Persistent identifier of particular redirect type.
     */
    const TYPE_ID = 'custom-url';

    /**
     * @var string
     */
    const TARGET_URL_FIELD_NAME = 'bc-page-redirect-custom-url';


    /**
     * @return string Human-readable description of redirect type.
     */
    public function getShortName(): string
    {
        return __('Redirect to custom URL', 'bc-page-redirect');
    }


    /**
     * @return string Redirect target location or empty string in case of invalid redirect data.
     */
    public function getTargetLocation(): string
    {
        return $this->data['url'] ?? '';
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
            'url' => isset($data['url']) ? \filter_var($data['url'], FILTER_SANITIZE_URL) : '',
        ];
    }


    /**
     * Output HTML snippet with form inputs to gather redirect data.
     */
    public function printFormFields(): void
    {
        $current_url = $this->data['url'] ?? '';
        ?>
            <p>
                <label for="bc-page-redirect-custom-url"><?= esc_html('URL to redirect to:', 'bc-page-redirect'); ?></label><br />
                <input type="text" name="<?= self::TARGET_URL_FIELD_NAME; ?>" id="bc-page-redirect-custom-url" value="<?= esc_attr($current_url); ?>" style="width: 20em; max-width: 100%;" />
            </p>
        <?php
    }


    /**
     * Read and store redirect data from given input type (either INPUT_POST or INPUT_GET).
     *
     * @see filter_input()
     */
    public function readFormInputData(int $input_type): void
    {
        $this->data = [
            'url' => \filter_input($input_type, self::TARGET_URL_FIELD_NAME, FILTER_VALIDATE_URL) ?: '',
        ];
    }
}
