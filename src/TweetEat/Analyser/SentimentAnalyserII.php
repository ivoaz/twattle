<?php

namespace TweetEat\Analyser;

use TweetEat\Collection\SentimentKeywordCollection;

class SentimentAnalyserII
{
    /**
     * @var array
     */
    private $lexicon;


    /**
     * Example lexicon:
     *     array(
     *        array('phrase' => 'not bad', 'rate' => 1),
     *        array('phrase' => 'not good', 'rate' => -1),
     *        array('phrase' => 'good', 'rate' => 1),
     *        array('phrase' => 'bad', 'rate' => -1)
     *     )
     *
     * @param array $lexicon
     * @param bool $sort whether lexicon needs to be sorted by a word count
     */
    public function __construct($lexicon, $sort = true)
    {
        $this->lexicon = $lexicon;

        // lexicon needs to be sorted so that strings like "not bad" has
        // higher priority compared to "bad"
        if ($sort) {
            usort($this->lexicon, function ($a, $b) {
                $ac = substr_count($a['phrase'], ' ');
                $bc = substr_count($b['phrase'], ' ');

                if ($ac == $bc) {
                    return 0;
                }

                return ($ac > $bc) ? -1 : 1;
            });
        }
    }

    /**
     * Gives a rating for the text by counting occurances of positive and
     * negative lexicon. Each positive occurance gives a +1 and each negative
     * occurance gives a -1 to the rating.
     *
     * @param string $text
     * @return int rating
     */
    public function analyse($text)
    {
        $result = array(
            'rating' => 0,
            'phrases' => array(),
        );

        $tLen = strlen($text);

        foreach ($this->lexicon as $phrase) {
            $pLen = strlen($phrase['phrase']);

            $start = 0;
            do {
                $start = stripos($text, $phrase['phrase'], $start);

                if (false === $start || $start > 0 && ctype_alpha($text[$start-1]) || ($end = $start+$pLen) < $tLen && ctype_alpha($text[$end])) {
                    continue;
                }
                
                foreach ($result['phrases'] as $p) {
                    if ($p['start'] <= $start && $p['end'] >= $start || $p['start'] <= $end && $p['end'] >= $end) {
                        continue 2;
                    }
                }

                $result['phrases'][] = array(
                    'phrase' => $phrase['phrase'],
                    'rate' => $phrase['rate'],
                    'start' => $start,
                    'end' => $end
                );

                $result['rating'] += $phrase['rate'];
                
            } while (false !== $start && ($start+=$pLen) < $tLen -1);
        }

        return $result;
    }
}
