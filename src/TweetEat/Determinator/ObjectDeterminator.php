<?php

namespace TweetEat\Determinator;

class ObjectDeterminator
{
    /**
     * @var array
     */
    protected $objects = null;

    /**
     * @param ObjectCollection $collection
     */
    public function __construct($collection)
    {
        $this->objects = $collection->findTopical(0);
    }

    /**
     * @param string $text
     * @return array
     */
    public function determine($text)
    {
        $objects = array();

        foreach ($this->objects as $object) {
            foreach ($object['keywords'] as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    $objects[] = $object['_id'];
                    break;
                }
            }
        }

        return $objects;
    }
}
