<?php

namespace TweetEat\Collection;

class TweetCollection
{
    /**
     * @var \MongoCollection
     */
    public $collection;

    /**
     * @param \MongoCollection $collection
     */
    public function __construct(\MongoCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \MongoCursor
     */
    public function findAll()
    {
        return $this->collection->find();
    }

    /**
     * @param array|object $tweet
     */
    public function insert($tweet)
    {
        if (is_array($tweet)) {
            $tweet['_id'] = $tweet['id_str'];
        }
        elseif (is_object($tweet)) {
            $tweet->_id = $tweet->id_str;
        }
        else {
            throw new \Exception('Invalid datatype of tweet, array or object expected.');
        }

        $this->collection->insert($tweet);
    }
}