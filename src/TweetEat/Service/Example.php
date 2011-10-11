<?php

namespace TweetEat\Service;

class Example implements ServiceInterface
{
    /**
     * @var \Pimple
     */
    protected $container;
    
    /**
     * @param \Pimple $container 
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Runs the service
     *
     * @param \Pimple $container
     */
    public function run()
    {
        echo 'This is ', $this->container['project_name'] . ".\n";
    }
}