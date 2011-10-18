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
     * @return Collection\ProductCollection;
     */
    public function getProductCollection()
    {
        static $collection;

        if (null === $collection) {
            $collection = new Collection\ProductCollection($this->db->products);
        }

        return $collection;
    }
}