<?php

namespace TweetEat\DependencyInjection;

class ServiceContainer extends \Pimple
{
    public function __construct()
    {
        $this['project_name'] = 'TweetEat';
        
        $this['example_class'] = 'TweetEat\\Service\\Example';
        $this['example'] = function ($c) {
            return new $c['example_class']($c);
        };
    }
}