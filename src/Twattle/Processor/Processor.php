<?php

namespace Twattle\Processor;

use Twattle\Determinator\ObjectDeterminator;
use Twattle\Determinator\SentimentDeterminator;
use Twattle\Normalizer\Normalizer;

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
     * @var engexp
     */
    protected $engexp;

    /**
     * @var notengexp
     */
    protected $notengexp;

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

        // configure language checker
        $patterns = array(" this ", " that ", " other ", " is ",  " the ", " are ", " he ", " at ", " we ", " can ", " do ", " or ", " and ", " to ");
        $this->engexp = '/('.implode('|', $patterns).')/i'; 
        $badpatterns = array("ž", "č", "ķ", "ā",  " foi ", " de ", " se ", "ī", "õ", "ū", "ē", "ŗ", "ļ", " voy ", " als ", " ik " ,"é", "ć", " er ");
        $this->notengexp = '/('.implode('|',$badpatterns).')/i'; 
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

        if (empty($tweet['objects'])) {
            // determine objects
            $tweet['objects'] = $this->objectDeterminator->determine($tweet['normalized_text']);
            if (empty($tweet['objects'])) {
                return false;
            }
        }

        // check that language is accepted
        if (!preg_match($this->engexp, $tweet['normalized_text'], $matches)) {
            return false;
        }
        if (preg_match($this->notengexp, $tweet['normalized_text'], $matches)) {
            return false;
        }

        // determine sentiment using keywords
        $tweet['sentiment'] = $this->sentimentAnalyser->analyse($tweet['normalized_text']);

        // determine sentiment using naive bayesian
        $tweet['sentiment']['naive_bayesian'] = $this->naiveBayesian->categorize($tweet['normalized_text']);

        return true;
    }
}
