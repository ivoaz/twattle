<?php

if (!isset($argv[1])) {
    exit("Please enter the name of the service you want to run.\n");
}

require_once __DIR__.'/bootstrap.php';

use TweetEat\DependencyInjection\ServiceContainer;
use TweetEat\Service\ServiceInterface;

$container = new ServiceContainer();

$serviceName = $argv[1];

if ($container->offsetExists($serviceName)) {
    $service = $container[$serviceName];
}
else {
    exit("Service \"$serviceName\" does not exist.\n");
}

if (!$service instanceof ServiceInterface) {
    exit("Service \"$serviceName\" cannot be runned.\n");
}

$service->run();
