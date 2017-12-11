<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\Concerns\AuthorizesMethodCalls;
use CollabCorp\Formatter\Concerns\InteractsThroughProxy;
use CollabCorp\Formatter\Concerns\IsAwareOfOtherFormatters;
use CollabCorp\Formatter\Concerns\ProcessesRules;
use CollabCorp\Formatter\Concerns\VerifiesAttributeTypes;
use CollabCorp\Formatter\Proxy;

class Formatter
{
    use ProcessesRules,
        VerifiesAttributeTypes,
        InteractsThroughProxy,
        AuthorizesMethodCalls;
    /**
     * The value that is being formatted
     *
     * @var mixed $value
     */
    protected $value;

    /**
     * Construct a new instance
     *
     * @param mixed $value
     */
    public function __construct($value = '')
    {
        if ($value === null || $value == '') {
            return null;
        }

        $this->value = trim($value);
    }

    /**
     * Named constructor.
     * 
     * @param  mixed $value 
     * @return \CollabCorp\Formatter\Formatter
     */
    public static function create($value = null)
    {
        return new static($value);
    }

    /**
     * Create a new Formatter instance.
     * 
     * @param  mixed $value 
     * @return \CollabCorp\Formatter\Formatter
     */
    public function newInstance($value = null)
    {
        return new static($value);
    }

    /**
     * This allows the Formatter to be called as a function.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function __invoke($value = null)
    {
        if ($value) {
            $this->setValue($value);
        }

        return $this->get();
    }

    /**
     * Cast the formatter to a string,
     * returning the result.
     *
     * @return string [Formatter result]
     */
    public function __toString()
    {
        return (string) $this->getResults();
    }

    /**
     * Tap into the Formatter.
     *
     * @param  \Closure | null $callback
     * @return mixed
     */
    public function tap($callback = null)
    {
        return tap($this, $callback);
    }

    /**
     * Set the value
     * @param mixed $value
     * @return CollabCorp\Formatter\Convert
     */
    public function setValue($val)
    {
        //automatically treat empty strings as null, this is due to some issues with laravel's convert empty string to null middleware
        if ($val == '') {
            $val =  null;
        }

        $this->value = $val;

        return $this;
    }

    /**
    * Get the value from the instance
    *
    * @return mixed $value
    */
    public function getValue()
    {
        return $this->value;
    }

    /**
    * Get the value from the instance
    *
    * @return mixed $value
    */
    public function get()
    {
        return $this->getValue();
    }

    /**
     * Get the resulting value.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->getValue();
    }

    /**
     * Reset the object state.
     *
     * @return $this
     */
    public function clear()
    {
        $this->value = '';

        return $this;
    }
}
