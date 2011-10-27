<?php

namespace TweetEat\Analyser;

class SentimentAnalyser
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
                $ac = substr_count($a['ngram'], ' ');
                $bc = substr_count($b['ngram'], ' ');

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
            'ngrams' => array(),
        );

        $tLen = strlen($text);

        foreach ($this->lexicon as $ngram) {
            $pLen = strlen($ngram['ngram']);

            $start = 0;
            do {
                $start = stripos($text, $ngram['ngram'], $start);

                if (false === $start || $start > 0 && ctype_alpha($text[$start-1]) || ($end = $start+$pLen) < $tLen && ctype_alpha($text[$end])) {
                    continue;
                }
                
                foreach ($result['ngrams'] as $p) {
                    if ($p['start'] <= $start && $p['end'] >= $start || $p['start'] <= $end && $p['end'] >= $end) {
                        continue 2;
                    }
                }

                $result['ngrams'][] = array(
                    'phrase' => $ngram['ngram'],
                    'rate' => $ngram['rate'],
                    'start' => $start,
                    'end' => $end
                );

                $result['rating'] += $ngram['rate'];
                
            } while (false !== $start && ($start+=$pLen) < $tLen -1);
        }

        return $result;
    }
}
