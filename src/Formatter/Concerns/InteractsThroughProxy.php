<?php

namespace CollabCorp\Formatter\Concerns;

use CollabCorp\Formatter\Exceptions\FormatterException;
use CollabCorp\Formatter\Proxy;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException as DriverNotFoundException;

trait InteractsThroughProxy
{
	use IsAwareOfOtherFormatters, Macroable {
		__call as macroCall;
	}

    /**
     * Call macros of proxy calls to other formatters.
     * 
     * @param  String $method
     * @param  array $args
     * @return CollabCorp\Formatter\Formatter
     */
    public function __call($method, $args = [])
    {
    	if (static::hasMacro($method)) {
    			$this->setValue($this->macroCall($method, $args));
    			return $this;
    	}

        return Proxy::call($method, $args, $this);
    }

    /**
     * Dynamically call the manager 
     *
     * @param  string  $method
     * @param  array   $args
     *
     * @throws \CollabCorp\Formatter\Exceptions\FormatterException 
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
    	try {
            return static::manager()->$method(...$args);
        } catch(DriverNotFoundException $e) {
            throw FormatterException::notFound($method);
        }
    }
}