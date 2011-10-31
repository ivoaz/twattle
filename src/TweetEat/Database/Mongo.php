<?php

namespace TweetEat\Database;

use TweetEat\Collection;

class Mongo
{
    /**
     * @var \MongoDB
     */
    private $db;

    /**
     * @param \MongoDB $db
     */
    public function __construct(\MongoDB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Collection\TweetCollection;
     */
    public function getTweetCollection()
    {
        static $collection;

        if (null === $collection) {
            $collection = new Collection\TweetCollection($this->db->tweets);
        }

        return $collection;
    }

    /**
     * @return Collection\ObjectCollection;
     */
    public function getObjectCollection()
    {
        static $collection;

        if (null === $collection) {
            $collection = new Collection\ObjectCollection($this->db->objects);
        }

        return $collection;
    }

    /**
     * @return Collection\LexiconCollection;
     */
    public function getLexiconCollection()
    {
        static $collection;

        if (null === $collection) {
            $collection = new Collection\LexiconCollection($this->db->lexicon);
        }

        return $collection;
    }
}