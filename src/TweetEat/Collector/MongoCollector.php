<?php

namespace TweetEat\Collector;

use TweetEat\Collection\TweetCollection;

class MongoCollector
{
    /**
     * @var TweetCollection
     */
    protected $collection;

    /**
     * @param TweetCollection $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param string $data
     */
    public function process($data)
    {
        $tweet = json_decode($data);
        $this->collection->insert($tweet);
    }
}
