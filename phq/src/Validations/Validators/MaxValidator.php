<?php

namespace PHQ\Validations\Validators;

class MaxValidator extends Validator
{

    protected $error = 'Le champs [FIELD] doit faire au maximum [0] caractères';

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        return mb_strlen($data[$this->field] ?? '') <= $this->params[0];
    }
}
