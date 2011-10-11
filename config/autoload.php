<?php

$src    = __DIR__.'/../src';
$vendor = __DIR__.'/../vendor';

require_once $vendor.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'TweetEat' => $src,
    'Symfony'  => $vendor.'/symfony/src',
));

$loader->registerPrefixes(array(
    'Pimple'              => $vendor.'/pimple/lib',
    'Phirehose'           => $vendor.'/phirehose/lib',
    'OauthPhirehose'      => $vendor.'/phirehose/lib',
    'UserstreamPhirehose' => $vendor.'/phirehose/lib',
));

$loader->register();
