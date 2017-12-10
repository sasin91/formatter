<?php

namespace CollabCorp\Formatter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Facade;
use InvalidArgumentException as DriverNotFoundException;

class Formatter extends Facade
{  
    /**
     * Create a BaseFormatter instance.
     * 
     * @param  mixed $value 
     * @return \CollabCorp\Formatter\Formatters\BaseFormatter
     */
    public static function create($value = null)
    {
        return static::getFacadeRoot()
            ->driver()
            ->setValue($value);
    }

    /**
     * Get the FormatterManager.
     *     
     * @return \CollabCorp\Formatter\FormatterManager
     */
    public static function manager()
    {
        return static::getFacadeRoot();
    }

    /**
     * Get a Formatter instance that implements given method
     * 
     * @param  string $method   
     * @return \CollabCorp\Formatter\Formatters\BaseFormatter 
     */
    public static function implementing($method)
    {
        return collect(static::manager()->available())->map(function ($driver) {
            return static::manager()->driver($driver);
        })->first(function ($formatter) use($method) {
            return method_exists($formatter, $method) && $formatter->whitelists($method);
        });
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return FormatterManager::class;
    }

    /**
    * Convert the input according to the rules
    * 
    * @param  array $rules
    * @param  array $request
    * @return array [Formatted request input results]
    */
    public static function convert($rules, $request)
    {
        return RuleProcessor::process($rules, $request);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        try {
        	return parent::__callStatic($method, $args);
        } catch (DriverNotFoundException $e) {
        	return Proxy::call($method, $args);
        }
    }
}