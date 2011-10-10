<?php

require_once __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/../vendor/pimple/lib/Pimple.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'TweetEat' => __DIR__.'/../src',
    'Symfony'  => __DIR__.'/../vendor/symfony/src',
));

$loader->registerNamespaceFallbacks(array(
    __DIR__.'/../vendor/pimple/lib',
));

$loader->register();
