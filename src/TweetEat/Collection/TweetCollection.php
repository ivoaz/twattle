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
     * @return \MongoCursor
     */
    public function findForProcessing()
    {
        return $this->collection->find(array(
            '$or' => array(
                array(
                    'normalized_text' => array('$exists' => false)
                ),
                array(
                    'objects' => array('$exists' => false)
                ),
                array(
                    'sentiment' => array('$exists' => false)
                )
            )
        ));
    }


    /**
     * Finds latest tweets containing given object
     *
     * @param string $id
     * @return \MongoCursor
     */
    public function findLatestContainingObject($id, $limit = 0)
    {
        return $this->collection->find(array(
            'objects' => $id,
        ))->sort(array(
            'objects.sentiment.determined_at' => -1,
        ))->limit($limit);
    }

    /**
     * Finds latest tweets containing given object and determined sentiment
     *
     * @param string $objectId
     * @return \MongoCursor
     */
    public function findForBattlePage($objectId, $limit = 0)
    {
        return $this->collection->find(array(
            'objects' => $objectId,
            'sentiment.rating' => array(
                '$ne' => 0,
            ),
        ))->sort(array(
            '_id' => -1,
        ))->limit($limit);
    }

    /**
     * @param array|object $tweet
     */
    public function insert($tweet)
    {
        $this->collection->insert($tweet);
    }

    /**
     * @param array|object $tweet
     */
    public function update($tweet)
    {
        $this->collection->update(array(
            '_id' => $tweet['_id']
        ), $tweet);
    }

    /**
     * @param mixed $tweet
     */
    public function remove($tweet)
    {
        $this->collection->remove(array(
            '_id' => is_array($tweet) ? $tweet['_id'] : $tweet,
        ));
    }
}
