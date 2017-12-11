<?php

namespace CollabCorp\Formatter\Concerns;

trait AuthorizesMethodCalls
{
	/**
     * Whitelist of the allowed methods to be called
     * 
     * @var Array $whiteList
     */
    protected $whiteList;

    /**
     * Determine if the method is not allowed to be called
     * 
     * @param  String $method
     * @return boolean
     */
    public function blacklists($method)
    {
        return ! $this->whitelists($method);
    }

    /**
     * Determine if the method is allowed to be called
     * 
     * @param  String $method
     * @return boolean
     */
    public function whitelists($method)
    {
        if (!property_exists($this, 'whiteList')) {
            $class = get_class($this);

            throw new \Exception("$class must have a whitelist property");
        }

        return in_array($method, $this->whiteList);
    }
}