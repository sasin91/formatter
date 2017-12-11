<?php

namespace CollabCorp\Formatter;

use CollabCorp\Formatter\Exceptions\FormatterException;

class Proxy
{
    /**
     * Proxy the call into the first formatter that can handle it.
     *
     * @param  string $method
     * @param  mixed $parameters
     * @param  object | null $previous [The previous formatter or value]
     * 
     * @throws \CollabCorp\Formatter\Exceptions\FormatterException
     * @return mixed
     */
    public static function call($method, $parameters, $previous = null)
    {
        $parameters = (Arr::expandable($parameters)) ? $parameters : [$parameters];

        $formatter = Formatter::implementing($method);

        throw_if(is_null($formatter), FormatterException::notFound($method));

        return $formatter
            ->newInstance(is_callable($previous) ? $previous() : $previous)
            ->$method(...$parameters);
    }
}
