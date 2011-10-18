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
     * Finds all tweets with undeterminated subject
     *
     * @return \MongoCursor
     */
    public function findWithoutSubject()
    {
        return $this->collection->find(array(
            '$or' => array(
                array('subjects' => array(
                    '$exists' => false,
                )),
                array('subjects' => array(
                    '$size' => 0,
                )),
            ),
        ));
    }

    /**
     * Finds all tweets with undeterminated mood for subjects
     *
     * @return \MongoCursor
     */
    public function findWithoutMood()
    {
        return $this->collection->find(array(
            'subjects' => array(
                '$exists' => true,
            ),
            'subjects.mood' => array(
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
     * Update tweets mood for subject
     *
     * @param float $tweetId
     * @param mixed $subjectId
     * @param int $value
     */
    public function updateMood($tweetId, $subjectId, $value)
    {
        $this->collection->update(array(
            '_id' => $tweetId,
            'subjects._id' => $subjectId
        ), array(
            '$set' => array(
                'subjects.$.mood.value' => $value,
                'subjects.$.mood.determinated_at' => new \MongoDate,
            )
        ));
    }

    /**
     * Add subject to the set of tweet subjects
     *
     * @param type $tweetId
     * @param type $subject
     */
    public function addSubject($tweetId, $subject)
    {
        $this->collection->update(array(
            '_id' => $tweetId,
        ), array(
            '$addToSet' => array(
                'subjects' => $subject,
            ),
        ));
    }
}
