<?php
/**
 * @package BC_Page_Redirect
 */

namespace BlueChip\PageRedirect;

/**
 * Any redirect type must extend this class, it declares basic API for every redirect to implement.
 */
abstract class AbstractRedirect
{
    /**
     * @var string Persistent identifier of particular redirect type.
     */
    const TYPE_ID = '';


    /**
     * @var array Redirect data (for calculation of target location)
     */
    protected $data = [];


    /**
     * @return string ID of redirect type (~ class constant).
     */
    public function getTypeId(): string
    {
        return static::TYPE_ID;
    }


    /**
     * @return array Redirect data.
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @param array $data
     * @return \BlueChip\PageRedirect\AbstractRedirect
     */
    public function setData(array $data): self
    {
        $this->data = static::sanitize($data);
        return $this;
    }


    /**
     * @return string Human-readable description of redirect type.
     */
    abstract public function getShortName(): string;


    /**
     * @return string Redirect target location or empty string in case of invalid redirect data.
     */
    abstract public function getTargetLocation(): string;


    /**
     * Sanitize redirect data - make sure correct keys are present and values have correct types.
     *
     * @param array $data Redirect data.
     * @return array Sanitized redirect data.
     */
    abstract protected function sanitize(array $data): array;


    /**
     * Output HTML snippet with form inputs to gather redirect data.
     */
    abstract public function printFormFields(): void;


    /**
     * Read and store redirect data from given input type (either INPUT_POST or INPUT_GET).
     *
     * @see filter_input()
     */
    abstract public function readFormInputData(int $input_type);
}
