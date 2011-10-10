<?php

namespace TweetEat\DependencyInjection;

class ServiceContainer extends \Pimple
{
    public function __construct()
    {
        $this['project_name'] = 'TweetEat';
    }
}