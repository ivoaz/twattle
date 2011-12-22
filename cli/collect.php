<?php

if (($lockFile = fopen(__DIR__.'/collect.lock', 'w')) === false) {
    exit("Failed to open lock file.\n");
}

if (flock($lockFile, LOCK_EX|LOCK_NB, $wouldBlock) === false || $wouldBlock) {
	exit("Failed to lock.\n");
}

require_once(__DIR__.'/../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$keywords = $database->getObjectCollection()->findTopicalKeywords();

if (empty($keywords)) {
    exit("The are no topical objects right now.\n");
}

$streamline = $container->getStreamline();
$streamline->setTrack($keywords);
$streamline->consume();

flock($lockFile, LOCK_UN);
fclose($lockFile);
