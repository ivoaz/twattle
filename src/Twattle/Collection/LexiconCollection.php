<?php

namespace Twattle\Collection;

class LexiconCollection
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
     * @param string $language
     * @return \MongoCursor
     */
    public function findByLanguage($language)
    {
        return $this->collection->find(array(
            'language' => $language,
        ));
    }
    
    /**
     * @param array|object $ngram
     */
    public function insert($ngram)
    {
        $this->collection->insert($ngram);
    }
}