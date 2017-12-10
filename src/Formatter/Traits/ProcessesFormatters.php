<?php

namespace CollabCorp\Formatter\Traits;

use Illuminate\Support\Str;
use CollabCorp\Formatter\Formatter;
use CollabCorp\Formatter\DateFormatter;
use CollabCorp\Formatter\MathFormatter;
use CollabCorp\Formatter\StringFormatter;

trait ProcessesFormatters
{


    /**
     * Process the formatters and convert the input
     * @param  Array $formatters
     * @param  Array $input
     * @return Array $convertedInput
     */
    protected function process($formatter, $formatters, $requestInput)
    {

        //for each of the specified formatters
        foreach ($formatters as $pattern=>$wantedFormatters) {
            $methodsToProcess = trim($wantedFormatters, "|");
            //determine if we need to process multiple conversions
            if (strpos($methodsToProcess, '|')) {
                $methodsToProcess  = explode('|', $methodsToProcess);
            }
            //first handle the current key if its explicity in the request input before we consider patterns
            if (array_key_exists($pattern, $requestInput)) {
                $requestInput[$pattern]= $formatter->handleExplicitExistingInputKey(
                    $formatter,
                    $methodsToProcess,
                    $pattern,
                    $requestInput
                );
            } else {
                //else process each of these as a pattern by looping the request input
                foreach ($requestInput as $inputKey => $inputValue) {
                    //do multiple conversions on each array element
                    if (is_array($methodsToProcess)) {
                        foreach ($methodsToProcess as $index => $method) {
                            $params = $formatter->getGivenParameters($method);
                            $method = $formatter->getMethodOnly($method);
                            //input fields can be arrays so lets run conversions on each item in that array
                            if (is_array($requestInput[$inputKey])) {
                                foreach ($requestInput[$inputKey] as $nestedKey=>$nestedValue) {
                                    if (Str::is($pattern, $inputKey)) {
                                        $requestInput[$inputKey][$nestedKey]= $formatter->callMethod(
                                            $method,
                                            $params,
                                            $requestInput[$inputKey][$nestedKey]

                                        );
                                    }
                                }
                            } else {
                                if (Str::is($pattern, $inputKey)) {
                                    $requestInput[$inputKey] = $formatter->callMethod(
                                        $method,
                                        $params,
                                        $requestInput[$inputKey]

                                    );
                                }
                            }
                        }
                    } else {
                        //else were only processing 1 conversion method
                        $params = $formatter->getGivenParameters($methodsToProcess);
                        $method = $formatter->getMethodOnly($methodsToProcess);

                        //but could still have array input
                        if (is_array($requestInput[$inputKey])) {
                            foreach ($requestInput[$inputKey] as $nestedKey=>$nestedValue) {
                                if (Str::is($pattern, $inputKey)) {
                                    $requestInput[$inputKey][$nestedKey]= $formatter->callMethod(
                                        $method,
                                        $params,
                                        $requestInput[$inputKey][$nestedKey]

                                    );
                                }
                            }
                        } else {
                            if (Str::is($pattern, $inputKey)) {
                                $requestInput[$inputKey] = $formatter->callMethod(
                                    $method,
                                    $params,
                                    $requestInput[$inputKey]

                                );
                            }
                        }
                    }
                }
            }
        }

        return $requestInput;
    }
    /**
     * Determined if our calling method needs params
     * @param  CollabCorp\Formatter\Formatter
     * @return boolean $needsParams
     */
    private function needsParams(Formatter $formatter, $func)
    {
        $reflection = new \ReflectionMethod($formatter, $func);
        $needsParams = $reflection->getNumberOfParameters() > 0;
        return $needsParams;
    }
    /**
     * Before processing input key as a pattern, be sure it is not a explicit key name in the request
     * @param  CollabCorp\Formatter\Convert $formatter
     * @param  $mixed $methodsToProcess
     * @param  String $pattern
     * @return String $requestInput[$pattern]
     */
    private function handleExplicitExistingInputKey($formatter, $methodsToProcess, $pattern, $requestInput)
    {
        if (is_array($methodsToProcess)) {
            foreach ($methodsToProcess as $index => $method) {
                $params = $formatter->getGivenParameters($method);
                $method = $formatter->getMethodOnly($method);
                if (is_array($requestInput[$pattern])) {
                    foreach ($requestInput[$pattern] as $nestedKey=>$nestedValue) {
                        $requestInput[$pattern][$nestedKey]= $formatter->callMethod(
                            $method,
                            $params,
                            $requestInput[$pattern][$nestedKey]

                        );
                    }
                } else {
                    $requestInput[$pattern] = $formatter->callMethod(
                        $method,
                        $params,
                        $requestInput[$pattern]

                    );
                }
            }
        } else {
            $params = $formatter->getGivenParameters($methodsToProcess);
            $method = $formatter->getMethodOnly($methodsToProcess);

            if (is_array($requestInput[$pattern])) {
                foreach ($requestInput[$pattern] as $nestedKey=>$nestedValue) {
                    $requestInput[$pattern][$nestedKey]= $formatter->callMethod(
                        $method,
                        $params,
                        $requestInput[$pattern][$nestedKey]

                    );
                }
            } else {
                $requestInput[$pattern] = $formatter->callMethod(
                    $method,
                    $params,
                    $requestInput[$pattern]
                );
            }
        }


        return $requestInput[$pattern];
    }

    /**
     * Call the current method during our process method
     * @param  String $method
     * @param  Array $params
     * @param  Array $requestInput
     * @param  String $currentInputKeyOrPattern
     */
    private function callMethod($method, $params, $value)
    {
        $formatter = $this->getNeededFormatter($method, $value);

        if ($formatter->needsParams($formatter, $method)) {
            $formatter->setValue($value);

            $formatter = call_user_func_array(array($formatter, $method), $params);

            $value = $formatter->get();
        } else {
            $formatter->setValue($value);
            $value = $formatter->{$method}()->get();
        }


        return $value;
    }

    /**
     * Determine what formatter is needed
     * @param  $method
     * @param  $value
     * @throws \Exception
     * @return CollabCorp\Formatter\Formatter
     */
    protected function getNeededFormatter($method, $value)
    {
        $formatter = null;
        $class = get_class($this);
        if (method_exists(StringFormatter::class, Str::camel($method))) {
            $formatter = new StringFormatter($value);
        } elseif (method_exists(MathFormatter::class, Str::camel($method))) {
            $formatter = new MathFormatter($value);
        } elseif (method_exists(DateFormatter::class, Str::camel($method))) {
            $formatter = new DateFormatter($value);
        }

        //make sure this is a white listed method
        if (!is_null($formatter)) {
            if (!$formatter->isAllowedToBeCalled($method)) {
                throw new \Exception("Call to undefined method $method on $class");
            }

            return $formatter;
        }



        throw new \Exception("Call to undefined method $method on $class");
    }
    /**
     * Get any parameters that are being passed in during our format method loop
     * @param  String
     * @return Array
     */
    private function getGivenParameters($currentMethod)
    {
        $values = [];

        if (strpos($currentMethod, ":")) {
            $values = str_replace(
                substr(
                    $currentMethod,
                    0,
                    strpos($currentMethod, ":")+1
                ),
                "",
                $currentMethod
            );

            $values = explode(",", $values);
        }

        return $values;
    }


    /**
     * Return only the method name if params are being passed in
     * @param  String $currentMethod
     * @return string $methodOnly
     */
    private function getMethodOnly($currentMethod)
    {
        $methodOnly = $currentMethod;
        //seperate the method name and param input via : delimiter
        if (strpos($currentMethod, ":")) {
            $methodOnly = str_replace(
                substr(

                    $currentMethod,
                    strpos($currentMethod, ":"),
                    strlen($currentMethod)
                ),
                "",
                $currentMethod
            );
        }

        return $methodOnly;
    }
}
