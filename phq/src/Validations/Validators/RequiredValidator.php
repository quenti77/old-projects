<?php

namespace PHQ\Validations\Validators;

class RequiredValidator extends Validator
{
    const STOP_ON_FAILURE = true;

    protected $error = 'Field "[FIELD]" is required';

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        return !empty($data[$this->field]);
    }
}
