<?php

namespace CollabCorp\Formatter\Concerns;

trait VerifiesAttributeTypes
{
    /**
     * Throw an exception for non numeric values
     *
     * @param  $method
     * @throws \Exception
     */
    protected function throwExceptionIfNonNumeric($method, $value =null)
    {
        if (is_null($value)) {
            $value = $this->value;
        }

        if (!is_numeric($value) && strlen($value)) {
            $class = get_class($this);
            throw new \Exception("$class: Non numeric value passed to {$method}, value given : {$value}");
        }
    }
}
