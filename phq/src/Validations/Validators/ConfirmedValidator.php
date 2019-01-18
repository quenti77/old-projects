<?php

namespace PHQ\Validations\Validators;

class ConfirmedValidator extends Validator
{

    protected $error = 'Le champs [FIELD] et [FIELD_CONFIRMED] ne sont pas identique';

    protected $fieldConfirm = null;

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        parent::setField($field);
        $this->fieldConfirm = "{$field}_confirmed";
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool
    {
        $field = $data[$this->field] ?? null;
        $fieldConfirmed = $data[$this->fieldConfirm] ?? null;

        return $fieldConfirmed !== null && $field === $fieldConfirmed;
    }

    public function getError(): string
    {
        $str = parent::getError();
        return str_replace('[FIELD_CONFIRMED]', $this->fieldConfirm, $str);
    }
}
