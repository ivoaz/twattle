<?php

namespace TweetEat\Processor;

use TweetEat\Determinator\ObjectDeterminator;
use TweetEat\Determinator\SentimentDeterminator;
use TweetEat\Normalizer\Normalizer;

class Processor
{
    /**
     * @var Normalizer
     */
    protected $normalizer;

    /**
     * @var ObjectDeterminator
     */
    protected $objectDeterminator;

    /**
     * @var SentimentAnalyser
     */
    protected $sentimentAnalyser;

    /**
     * @param Database $db
     * @param Normalizer $normalizer
     * @param ObjectDeterminator $objectDeterminator
     * @param SentimentAnalyser $sentimentAnalyser
     */
    public function __construct($normalizer, $objectDeterminator, $sentimentAnalyser)
    {
        $this->normalizer = $normalizer;
        $this->objectDeterminator = $objectDeterminator;
        $this->sentimentAnalyser = $sentimentAnalyser;
    }

    /**
     * Processes tweet
     *
     * @param array $tweet
     * @return bool
     */
    public function process(&$tweet)
    {
        $tweet['normalized_text'] = $this->normalizer->normalize($tweet['original_text']);

        $tweet['objects'] = $this->objectDeterminator->determine($tweet['normalized_text']);
        
        //eng check goes here
        if (empty($tweet['objects'])) {
            return false;
        }

        $tweet['sentiment'] = $this->sentimentAnalyser->analyse($tweet['normalized_text']);

        return true;
    }
}
