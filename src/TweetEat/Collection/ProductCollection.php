<?php

namespace TweetEat\Collection;

class ProductCollection
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
     * @param array|object $product
     */
    public function insert($product)
    {
        $this->collection->insert($product);
    }
}