<?php

namespace PHQ\Validations\Validators;

use PHQ\Validations\DefaultValidator;
use PHQ\Validations\IValidator;

class Validator implements IValidator
{
    const STOP_ON_FAILURE = false;

    /**
     * @var string $error
     */
    protected $error = 'Error in field : "[FIELD]"';

    /**
     * @var array $params
     */
    protected $params = [];

    /**
     * @var string $field
     */
    protected $field;

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @param array $params
     */
    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        $err = $this->error;
        $err = str_replace('[FIELD]', $this->field, $err);

        foreach ($this->params as $index => $value) {
            $err = str_replace("[{$index}]", $value, $err);
        }

        return $err;
    }
}
