<?php

namespace CollabCorp\Formatter;

class Arr extends \Illuminate\Support\Arr
{
    /**
     * Determine if the array is expandable.
     *     
     * @param  array $array 
     * @return boolean             
     */
    public static function expandable($array)
    {
        $keys = array_keys($array);

        return count(array_filter($array, 'is_numeric', ARRAY_FILTER_USE_KEY)) === count($array);
    }
}