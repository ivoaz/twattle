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
     * Finds all tweets with undetermined object
     *
     * @return \MongoCursor
     */
    public function findWithUndeterminedObject()
    {
        return $this->collection->find(array(
            '$or' => array(
                array('objects' => array(
                    '$exists' => false,
                )),
                array('objects' => array(
                    '$size' => 0,
                )),
            ),
        ));
    }

    /**
     * Finds all tweets with undetermined sentiment
     *
     * @return \MongoCursor
     */
    public function findWithUndeterminedSentiment()
    {
        return $this->collection->find(array(
            'objects' => array(
                '$exists' => true,
            ),
            'objects.sentiment' => array(
                '$exists' => false,
            ),
        ));
    }

    /**
     * @param array|object $tweet
     */
    public function insert($tweet)
    {
        $this->collection->insert($tweet);
    }

    /**
     * @param float $id
     */
    public function remove($id)
    {
        $this->collection->remove(array(
            '_id' => $id,
        ));
    }

    /**
     * Update tweets sentiment for object
     *
     * @param float $tweetId
     * @param mixed $objectId
     * @param int $value
     */
    public function updateSentiment($tweetId, $objectId, $value)
    {
        $this->collection->update(array(
            '_id' => $tweetId,
            'objects._id' => $tweetId
        ), array(
            '$set' => array(
                'objects.$.sentiment.value' => $value,
                'objects.$.sentiment.determined_at' => new \MongoDate,
            )
        ));
    }

    /**
     * Add object to the set of tweet object
     *
     * @param type $tweetId
     * @param type $object
     */
    public function addObject($tweetId, $object)
    {
        $this->collection->update(array(
            '_id' => $tweetId,
        ), array(
            '$addToSet' => array(
                'objects' => $object,
            ),
        ));
    }
}
