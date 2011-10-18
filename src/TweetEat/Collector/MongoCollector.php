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
        $tweet = array(
            'original' => json_decode($data, true),
            'collected_at' => new \MongoDate(),
        );

        if (!isset($tweet['original']['id'])) {
            @error_log('TweetEat/Collector/MongoCollector: Got tweet without id, skipping.', 0);
            return;
        }
        
        $tweet['_id'] = $tweet['original']['id'];
        
        $this->collection->insert($tweet);
    }
}
