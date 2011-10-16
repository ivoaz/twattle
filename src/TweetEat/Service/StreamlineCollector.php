<?php

namespace TweetEat\Service;

use TweetEat\Collector\MongoCollector;
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
    public function run()
    {
        $username = $this->container['twitter_api_username'];
        $password = $this->container['twitter_api_password'];
        $keywords = $this->container['twitter_streamline_keywords'];

        $dir = $this->container['sys_root_dir'].'/data/tweets';
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                throw new \Exception('Can\'t create directory ' . $dir . ' for storing tweets.');
            }
        }

        $database = $this->container['database'];
        $collection = $database->getTweetCollection();
        $collector = new MongoCollector($collection);

        $streamline = new FilterStreamline($username, $password, $collector);
        $streamline->setTrack(explode(',', $keywords));
        $streamline->consume();
    }
}