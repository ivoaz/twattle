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
     */
    public function run()
    {
        echo 'This is ', $this->container['config']['tweeteat']['project_name'] . ".\n";
    }
}