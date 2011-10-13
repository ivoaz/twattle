<?php

namespace TweetEat\Service;

use TweetEat\Collector\FileCollector;
use TweetEat\Streamline\FilterStreamline;

class StreamlineCollector implements ServiceInterface
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
    public function run($parameters = array())
    {
        $config = $this->container['config'];

        $username = $config['twitter_api']['username'];
        $password = $config['twitter_api']['password'];

        $dir = $config['root_dir'].'/data/tweets';
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new \Exception('Can\'t create directory ' . $dir . ' for storing tweets.');
            }
        }

        $collector = new FileCollector($dir.'/'.date('YmdHis'));

        $streamline = new FilterStreamline($username, $password, $collector);
        
        $streamline->setTrack(explode(',', $config['streamline_collector']['keywords']));

        $streamline->consume();
    }
}