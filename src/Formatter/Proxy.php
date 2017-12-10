<?php

namespace CollabCorp\Formatter;

class Proxy
{
    /**
     * Proxy the call into the first formatter that can handle it.
     *
     * @param  string $method
     * @param  mixed $parameters
     * @param  object | null $previous [The previous formatter or value]
     * @return mixed
     */
    public static function call($method, $parameters, $previous = null)
    {
        $parameters = (Arr::expandable($parameters)) ? $parameters : [$parameters];

        $formatter = Formatter::implementing($method);

        if (is_null($formatter)) {
            throw new \Exception("No formatter implements [{$method}].");
        }

        if ($previous) {
            $formatter->setValue(is_callable($previous) ? $previous() : $previous);
        }

        return $formatter->$method(...$parameters);
    }
}
