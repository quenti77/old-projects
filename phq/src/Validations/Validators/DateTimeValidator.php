<?php

namespace PHQ\Validations\Validators;

use DateTime;
use Exception;

class DateTimeValidator extends Validator
{

    protected $error = 'Le champs [FIELD] doit être une date valide';

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        if ($this->params[0] === true) {
            $this->params[0] = 'Y-m-d H:i:s';
        }

        $result = $data[$this->field] = DateTime::createFromFormat($this->params[0], $data[$this->field]);
        if ($result === false) {
            return false;
        }

        return true;
    }
}
