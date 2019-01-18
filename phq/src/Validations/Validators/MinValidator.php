<?php

namespace PHQ\Validations\Validators;

class MinValidator extends Validator
{

    protected $error = 'Le champs [FIELD] doit au moins faire [0] caractères';

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        return mb_strlen($data[$this->field] ?? '') >= $this->params[0];
    }
}
