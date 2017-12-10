<?php

namespace CollabCorp\Formatter\Formatters;

use CollabCorp\Formatter\Proxy;

class BaseFormatter
{
    /**
     * The value that is being formatted
     * @var [mixed] $value
     */
    protected $value;

    /**
     * Whitelist of the allowed methods to be called
     * @var Array $whiteList
     */
    protected $whiteList;

    /**
     * Construct a new instance
     * @param [mixed] $value
     * @return CollabCorp\Formatter\Convert
     */
    public function __construct($value = '')
    {
        if ($value === null || $value == '') {
            return null;
        }

        $this->value = trim($value);
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

    public function getResults()
    {
        return tap($this->getValue(), function () {
            $this->clear();
        });
    }

    public function clear()
    {
        $this->value = '';

        return $this;
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


    /**
     * Determine if the method is not allowed to be called
     * 
     * @param  String $method
     * @return boolean
     */
    public function blacklists($method)
    {
        return ! $this->whitelists($method);
    }

    /**
     * Determine if the method is allowed to be called
     * 
     * @param  String $method
     * @return boolean
     */
    public function whitelists($method)
    {
        if (!property_exists($this, 'whiteList')) {
            $class = get_class($this);

            throw new \Exception("$class must have a whitelist property");
        }

        return in_array($method, $this->whiteList);
    }

    /**
     * Call other formatter methods using magic method
     * 
     * @param  String $method
     * @param  array $args
     * @return CollabCorp\Formatter\Formatter
     */
    public function __call($method, $args = [])
    {
        return Proxy::call($method, $args, $this);
    }
}
