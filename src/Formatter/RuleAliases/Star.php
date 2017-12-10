<?php

namespace CollabCorp\Formatter\RuleAliases;

class Star 
{
	/**
	 * Pluck all values matching key.
	 * 
	 * @param  string $key   
	 * @param  array $input 
	 * @return array        
	 */
	public function __invoke($key, $input)
	{	
		// first we'll strip any * from the key.
		$withoutAlias = str_replace('*', '', $key);
		
		// next we'll grab the values.
		if (substr_count($key, '*') === 1) {
			if (starts_with($key, '*')) {
				$values = $this->after($withoutAlias, $input);
			} else {
				$values = $this->before($withoutAlias, $input);
			}
		} else {
			$values = $this->contains($withoutAlias, $input);
		}

		// Finally, return the results.
		return [
			'key' => $withoutAlias,
			'values' => $values
		];
	}

	/**
	 * Filter values that contains with given key
	 * 
	 * @param  string $withoutAlias   
	 * @param  array $input 
	 * @return array        
	 */
	protected function contains($withoutAlias, $input)
	{
		return array_filter($input, function ($key) use($withoutAlias) {
			return str_contains($key, $withoutAlias);
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Filter values that starts with given key
	 * 
	 * @param  string $withoutAlias   
	 * @param  array $input 
	 * @return array        
	 */
	protected function before($withoutAlias, $input)
	{	
		return array_filter($input, function ($key) use($withoutAlias) {
			return starts_with($key, $withoutAlias);
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * Filter the values that ends with key
	 * 	
	 * @param  string $withoutAlias   
	 * @param  array $input 
	 * @return array        
	 */
	protected function after($withoutAlias, $input)
	{
		return array_filter($input, function ($key) use($withoutAlias) {
			return ends_with($key, $withoutAlias);
		}, ARRAY_FILTER_USE_KEY);
	}
}