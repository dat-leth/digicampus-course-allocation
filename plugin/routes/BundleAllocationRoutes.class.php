<?php

class BundleAllocationRoutes extends \RESTAPI\RouteMap
{
    public function before() {

    }

    /**
     * Greets the caller
     *
     * @get /bundleallocation
     * @get /bundleallocation/:name
     * @condition name ^\w+$
     */
    public function sayHello($name = 'world')
    {
        return sprintf('Hello %s!', $name);
    }

    public function after() {

    }
}