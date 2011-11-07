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

        if (isset($tweet['original']['id_str'])) {
            $tweet['_id'] = $tweet['original']['id_str'];
        }
        elseif (isset($tweet['original']['id'])) {
            $tweet['_id'] = (string)$tweet['original']['id'];
        }
        else {
            // can't collect tweet without id
            return;
        }
        
        $this->collection->insert($tweet);
    }
}
