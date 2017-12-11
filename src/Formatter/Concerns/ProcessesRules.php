<?php

namespace CollabCorp\Formatter\Concerns;

use CollabCorp\Formatter\RuleProcessor;

trait ProcessesRules 
{
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
}