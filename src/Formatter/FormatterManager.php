<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\Formatters\BaseFormatter;
use CollabCorp\Formatter\Formatters\DateFormatter;
use CollabCorp\Formatter\Formatters\MathFormatter;
use CollabCorp\Formatter\Formatters\StringFormatter;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use ReflectionClass;

class FormatterManager extends Manager 
{
	/**
	 * Array of drivers available by method.
	 * 
	 * @var array
	 */
	protected $availableByMethod = [];
    
	/**
     * Get the names of all the available drivers.
     * 		
     * @return array 
     */
    public function available()
    {
    	if (empty($this->availableByMethod)) {
    		$this->buildAvailableByMethod();
    	}

    	return array_merge(
    		array_keys($this->customCreators), 
    		$this->availableByMethod
    	);
    }

    /**
     * Build the array of drivers available by method 
     * 
     * @return array
     */
    protected function buildAvailableByMethod()
    {
    	return $this->availableByMethod = collect((new ReflectionClass($this))->getMethods())
	    	->map->name
	    	->filter(function ($method) {
	    		return Str::startsWith($method, 'create') && Str::endsWith($method, 'Driver');
	    	})
	    	->map(function ($method) {
	    		return Str::replaceLast(
	    			'Driver',
	    			'',
	    			Str::after($method, 'create')
	    		);
	    	})
	    	->toArray();
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
    	return 'default';
    }

    public function createDefaultDriver()
    {
    	return new BaseFormatter;
    }

	public function createMathDriver()
	{
		return new MathFormatter;
	}

	public function createStringDriver()
	{
		return new StringFormatter;
	}

	public function createDateDriver()
	{
		return new DateFormatter;
	}
}