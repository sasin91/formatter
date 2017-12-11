<?php

namespace CollabCorp\Formatter\Concerns;

use CollabCorp\Formatter\FormatterManager;

trait IsAwareOfOtherFormatters
{
	/**
	 * the formatter manager.
	 * @var \CollabCorp\Formatter\FormatterManager
	 */
	protected static $manager;

    /**
     * Get the FormatterManager.
     *     
     * @return \CollabCorp\Formatter\FormatterManager
     */
    public static function manager()
    {
        if (static::$manager) {
        	return static::$manager;
        }

        return static::$manager = resolve(FormatterManager::class);
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
}