<?php

namespace CollabCorp\Formatter;

class RuleProcessor
{
    /**
     * Process given input with formatters.
     *
     * @param  array $rules
     * @param  mixed $input
     * @return array
     */
    public static function process($rules, $input)
    {
    	$results = [];
    	foreach ($rules as $key => $value) {
    		$rule = new Rule($value, $key);
    		$values = $rule->apply($input);

            $results[] = $values;
    	}

    	$results = array_collapse($results);

    	return array_merge($input, $results);
    }
}
