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
        $this->collection->insert($tweet);
    }
}