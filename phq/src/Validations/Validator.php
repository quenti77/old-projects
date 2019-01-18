<?php

namespace PHQ\Validations;

use ArrayAccess;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Validator
 * @package PHQ\Validations
 */
class Validator implements ArrayAccess
{
    /**
     * @var IValidator[][] $rules
     */
    private $rules = [];

    /**
     * @var bool $success
     */
    private $success = true;

    /**
     * @var IValidator[] $errors
     */
    private $errors = [];

    /**
     * @var array $fields
     */
    private $fields = [];

    /**
     * Validator constructor.
     * @param array $rules
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct(array $rules = [])
    {
        $this->addRules($rules);
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $rules
     * @return Validator
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function addRules(array $rules = []): self
    {
        foreach ($rules as $field => $rule) {
            $this->addRule($field, $rule);
        }
        return $this;
    }

    /**
     * @param string $field
     * @param $parts
     * @return Validator
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function addRule(string $field, $parts): self
    {
        if (is_string($parts)) {
            $parts = explode('|', $parts);
        }

        $fieldRules = [];
        foreach ($parts as $name => $params) {
            if (is_int($name)) {
                $split = explode(':', $params);
                $name = $split[0];
                $params = $split[1] ?? 'true';
            }

            $params = array_map([$this, 'strToValue'], explode(',', $params));

            $validator = app()->getContainer()->make($name);
            if ($validator instanceof IValidator) {
                $validator->setField($field);
                $validator->addParams($params);
                $fieldRules[] = $validator;
            }
        }

        $this->rules[] = $fieldRules;
        return $this;
    }

    /**
     * @param ServerRequestInterface|array $request
     * @return bool
     * @throws Exception
     */
    public function validate($request): bool
    {
        if ($request instanceof ServerRequestInterface) {
            $request = $request->getParsedBody();
        }

        if (!is_array($request)) {
            throw new Exception('Paramètre incorrecte');
        }

        $this->fields = $request;
        foreach ($this->rules as $rules) {
            $this->processRules($rules, $this->fields);
        }

        return $this->success;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset] ?? null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('Set forbiden');
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception('Unset forbiden');
    }

    /**
     * @param IValidator[] $rules
     * @param array $request
     */
    private function processRules(array $rules, array $request): void
    {
        foreach ($rules as $rule) {
            if (!$rule->validate($request)) {
                $this->success = false;
                $this->errors[] = $rule->getError();

                if ($rule::STOP_ON_FAILURE ?? false) {
                    return;
                }
            }
        }
    }

    /**
     * @param string $param
     * @return bool|int|string
     */
    private function strToValue(string $param)
    {
        if (in_array(mb_strtolower($param), ['true', 'false'])) {
            return mb_strtolower($param) === 'true';
        }

        if (intval($param) == $param) {
            return intval($param);
        }

        return $param;
    }
}
