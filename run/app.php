<?php

require_once __DIR__.'/../config/bootstrap.php';

use TweetEat\DependencyInjection\ServiceContainer;

$container = new ServiceContainer();

echo 'This is ', $container['project_name'], "\n";
