<?php

namespace PHQ\Validations;

/**
 * Interface IValidator
 * @package PHQ\Validations
 */
interface IValidator
{

    /**
     * @return string
     */
    public function getError(): string;

    /**
     * @param string $field
     */
    public function setField(string $field): void;

    /**
     * @param array $params
     */
    public function addParams(array $params): void;

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array &$data): bool;
}
