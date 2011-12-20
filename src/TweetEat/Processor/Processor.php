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
     * @var NaiveBayesian
     */
    protected $naiveBayesian;

    /**
     * @param Database $db
     * @param Normalizer $normalizer
     * @param ObjectDeterminator $objectDeterminator
     * @param SentimentAnalyser $sentimentAnalyser
     * @param NaiveBayesian $naiveBayesian
     */
    public function __construct($normalizer, $objectDeterminator, $sentimentAnalyser, $naiveBayesian)
    {
        $this->normalizer = $normalizer;
        $this->objectDeterminator = $objectDeterminator;
        $this->sentimentAnalyser = $sentimentAnalyser;
        $this->naiveBayesian = $naiveBayesian;
    }

    /**
     * Processes tweet
     *
     * @param array $tweet
     * @return bool
     */
    public function process(&$tweet)
    {
        // normalize text
        $tweet['normalized_text'] = $this->normalizer->normalize($tweet['original_text']);

        if (!empty($tweet['objects'])) {
            // determine objects
            $tweet['objects'] = $this->objectDeterminator->determine($tweet['normalized_text']);
            if (empty($tweet['objects'])) {
                return false;
            }
        }

        // determine sentiment using keywords
        $tweet['sentiment'] = $this->sentimentAnalyser->analyse($tweet['normalized_text']);

        // determine sentiment using naive bayesian
        $tweet['sentiment']['naive_bayesian'] = $this->naiveBayesian->categorize($tweet['normalized_text']);

        return true;
    }
}
