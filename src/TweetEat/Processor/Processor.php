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
    * @var engexp
    */
    protected $engexp
    
    /**
    * @var notengexp
    */
    protected $notengexp
    
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
        
        $patterns = array(" this ", " that ", " other ", " is ",  " the ", " are ", " he ", " at ", " we ", " can ", " do ", " or ", " and ", " to ");
        $this->engexp = '/(' .implode('|', $patterns) .')/i'; 
        $badpatterns = array("ž", "č", "ķ", "ā",  " foi ", " de ", " se ", "ī", "õ", "ū", "ē", "ŗ", "ļ", " voy ", " als ", " ik " ,"é", "ć", " er ");
        $this->notengexp = '/(' .implode('|',$badpatterns) .')/i'; 
        
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
        
        
        if (!preg_match($this->engexp, $tweet['normalized_text'], $matches)) {
            return false;
        }
       
       if(preg_match($this->notengexp, $tweet['normalized_text'], $matches){
            return false;
       }


        $tweet['sentiment'] = $this->sentimentAnalyser->analyse($tweet['normalized_text']);

        return true;
    }
}
