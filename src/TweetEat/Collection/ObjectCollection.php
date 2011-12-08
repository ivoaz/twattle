<?php

namespace TweetEat\Collection;

class ObjectCollection
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
     * @param array $fields
     * @return \MongoCursor
     */
    public function findAll($fields = array())
    {
        return $this->collection->find(array(), $fields);
    }

    /**
     * @return \MongoCursor
     */
    public function findKeywords()
    {
        $result = $this->collection->db->command(array('distinct' => 'objects', 'key' => 'keywords'));

        // for old mongodb versions
        if (count($result['values']) != count($result['values'], 1)) {
            $keywords = array();
            foreach ($result['values'] as $item) {
                $keywords = array_merge($keywords, $item);
            }
            return array_unique($keywords);
        }

        return $result['values'];
    }

    /**
     * Finds keywords of products that are topical at the time
     *
     * @return \MongoCursor
     */
    public function findTopicalKeywords()
    {
        $date = new \MongoDate();
        $result = $this->collection->db->command(array(
            'distinct' => 'objects',
            'key' => 'keywords',
            'query' => array(
                'topical_from' => array('$lte' => $date),
                'topical_till' => array('$gte' => $date),
            )
        ));

        // for old mongodb versions
        if (count($result['values']) != count($result['values'], 1)) {
            $keywords = array();
            foreach ($result['result'] as $item) {
                $keywords = array_merge($keywords, $item);
            }
            return array_unique($keywords);
        }
        
        return $result['values'];
    }

    /**
     * Finds objects that are topical at the time
     *
     * @param string $category
     * @param int $limit
     *
     * @return \MongoCursor
     */
    public function findTopical($category = null, $limit = 2)
    {
        $date = new \MongoDate();
        return $this->collection->find(array(
            'topical_from' => array('$lte' => $date),
            'topical_till' => array('$gte' => $date),
        ))->limit($limit);
    }

    /**
     * @param array|object $object
     */
    public function insert($object)
    {
        $this->collection->insert($object);
    }
}