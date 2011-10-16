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
        return new Collection\TweetCollection($this->db->tweets);
    }
}