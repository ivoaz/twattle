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
     *        array('ngram' => 'not bad',  'rate' => 1),
     *        array('ngram' => 'not good', 'rate' => -1),
     *        array('ngram' => 'good',     'rate' => 1),
     *        array('ngram' => 'bad',      'rate' => -1)
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
     * Gives a sentiment rating for the text by finding occurances of lexicon
     * n-grams and summing their rate.
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
            $ngLen = strlen($ngram['ngram']);

            $start = 0;
            do {
                $start = stripos($text, $ngram['ngram'], $start);

                if (false === $start || $start > 0 && ctype_alpha($text[$start-1]) || ($end = $start+$ngLen) < $tLen && ctype_alpha($text[$end])) {
                    continue;
                }
                
                foreach ($result['ngrams'] as $ng) {
                    if ($ng['start'] <= $start && $ng['end'] >= $start || $ng['start'] <= $end && $ng['end'] >= $end) {
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
                
            } while (false !== $start && ($start+=$ngLen) < $tLen -1);
        }

        return $result;
    }
}
