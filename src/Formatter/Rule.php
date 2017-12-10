<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\Proxy;
use Illuminate\Support\Str;

class Rule
{
    /**
     * the attributes key for extracting values from input.
     *
     * @var string
     */
    public $attribute;

    /**
     * The values to apply the rule methods to.
     *
     * @var array
     */
    public $values = [];

    /**
     * The methods to invoke.
     *
     * @var array
     */
    public $methods = [];

    /**
     * Array of symbol to method aliases.
     *
     * @var array
     */
    protected static $aliases = [];

    /**
     * Alias a symbol to an invokable object or callback.
     *
     * @param  string   $key
     * @param  callable $callable
     * @return void
     */
    public static function alias($key, callable $callable)
    {
        static::$aliases[$key] = $callable;
    }

    /**
     * Construct the rule.
     *
     * @param string $value
     * @param string $key
     */
    public function __construct($value, $key)
    {
        $this->methods = array_filter(explode('|', $value));
        $this->attribute = $key;
    }

    /**
     * Dynamically proxy a call to a formatter.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return Proxy::call($method, $parameters);
    }

    /**
     * Extract the values from the input.
     *
     * @param  array $input
     * @return array
     */
    public function extractValues($input)
    {
    	$key = $this->attribute;

        if ($callable = $this->getAlias($key)) {
        	tap($callable($key, $input), function ($results) {
        		$this->attribute = $results['key'];
        		$this->values = $results['values'];
        	});
        } else {
            $this->values = array_get($input, $key);
        }

        return array_wrap($this->values);
    }
	
    /**
     * Get the callable for the alias, if any.
     * 	
     * @param  string $key 
     * @return callable | null      
     */
    public function getAlias($key)
    {
    	foreach (static::$aliases as $alias => $callable) {
    		if (str_contains($key, $alias)) {
    			return $callable;
    		}
    	}
    }

    /**
     * Apply the rule to the input
     *
     * @param  array $input
     * @return array
     */
    public function apply($input)
    {   
        // First, lets extract the relevant values from the request input.
        $values = $this->extractValues($input);

        // Next lets compose the transformations to be run on the values.
        $transformations = collect($this->methods)->map(function ($method) {
            // Grab the parameters & shift the method off.
            $parameters = explode(':', $method);
            array_shift($parameters);

            return [
                'method' => Str::before($method, ':'),
                'parameters' => $parameters
            ];
        });

        // Run the transformations.
        // Technically, running transformations isn't per-sé a Rule's responsibility..But oh well.
        foreach ($values as &$value) {
            $transformations->each(function ($formatter) use(&$value) {
                $value = Proxy::call($formatter['method'], $formatter['parameters'], $value)->getValue();
            });
        }

        // We need to iterate the values twice, 
        // because changing the array while looping,
        // will cause it to be iterated twice, 
        // which may result in undesired transformations or exceptions.
        foreach ($values as $key => $value) {
            if (is_numeric($key)) {
                unset($values[$key]);
                $values[$this->attribute] = $value;
            } 
        }

        // finally, lets clean up by clearing the formatter instances.
        return tap($values, function () use($transformations) {
            $transformations->each(function ($formatter) {
                Proxy::call($formatter['method'], $formatter['parameters'])->clear();
            });
        });
    }
}
