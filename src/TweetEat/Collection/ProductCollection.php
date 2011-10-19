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
     * @return \MongoCursor
     */
    public function findKeywords()
    {
        $result = $this->collection->db->command(array('distinct' => 'products', 'key' => 'keywords'));
        return $result['values'];
    }
    
    /**
     * @param array|object $product
     */
    public function insert($product)
    {
        $this->collection->insert($product);
    }
}