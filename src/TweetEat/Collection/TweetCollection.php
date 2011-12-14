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
     * Finds latest tweets containing given object
     *
     * @param string $id
     * @return \MongoCursor
     */
    public function findLatestContainingObject($id, $limit = 0)
    {
        return $this->collection->find(array(
            'objects._id' => $id,
        ))->sort(array(
            'objects.sentiment.determined_at' => -1,
        ))->limit($limit);
    }

    /**
     * Finds latest tweets containing given object and determined sentiment
     *
     * @param string $id
     * @return \MongoCursor
     */
    public function findLatestWithSentimentContainingObject($id, $limit = 0)
    {
        return $this->collection->find(array(
            'objects._id' => $id,
            'objects.sentiment.rating' => array(
                '$ne' => 0,
            ),
        ))->sort(array(
            'objects.0.sentiment.determined_at' => -1,
        ))->limit($limit);
    }

    /**
     * Generates object statistics
     *
     * @param string $id
     * @return array
     */
    public function getObjectStats($id)
    {
        $stats = array();

        $stats['total'] = $this->collection->count(array(
            'objects._id' => $id,
        ));

        $stats['pos'] = $this->collection->count(array(
            'objects._id' => $id,
            'objects.sentiment.rating' => array(
                '$gt' => 0,
            ),
        ));

        $stats['neg'] = $this->collection->count(array(
            'objects._id' => $id,
            'objects.sentiment.rating' => array(
                '$lt' => 0,
            ),
        ));

        $stats['spam'] = 'N/A';

        return $stats;
    }

    /**
     * @param array|object $tweet
     */
    public function insert($tweet)
    {
        $this->collection->insert($tweet);
    }

    /**
     * @param string $id
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
     * @param array $sentiment
     */
    public function updateSentiment($tweetId, $objectId, $sentiment)
    {
        $sentiment['determined_at'] = new \MongoDate();
        
        $this->collection->update(array(
            '_id' => $tweetId,
            'objects._id' => $objectId
        ), array(
            '$set' => array(
                'objects.$.sentiment' => $sentiment
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
