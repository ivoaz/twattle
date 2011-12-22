<?php

namespace Twattle\Collector;

use Twattle\Collection\TweetCollection;

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
    public function collect($data)
    {
        $encoded = json_decode($data, true);
        $tweet = array(
            'original_text' => $encoded['text'],
            'collected_at' => new \MongoDate(),
            'created_at' => @$encoded['created_at'],
            'retweeted' => @$encoded['retweeted'],
            'user_id' => @$encoded['user']['id_str'],
        );
        
        if (isset($encoded['id_str'])) {
            $tweet['_id'] = $encoded['id_str'];
        }
        elseif (isset($encoded['id'])) {
            $tweet['_id'] = (string)$encoded['id'];
        }
        else {
            // can't collect tweet without id
            return;
        }
        
        $this->collection->insert($tweet);
    }

    /**
     * @param string $data
     */
    public function process($data)
    {
        $this->collect($data);
    }
}
