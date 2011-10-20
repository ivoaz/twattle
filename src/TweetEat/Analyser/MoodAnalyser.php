<?php

namespace TweetEat\Analyser;

class MoodAnalyser
{
    protected $positiveKeywords;
    protected $negativeKeywords;

    const KEYWORD_MATCH_PREG = "/(#keywords#)/i";

    /**
     * @param string $keywordsFile (json)
     */
    public function __construct($keywordsFile)
    {
        $keywords = file_get_contents($keywordsFile);

        $keywords = json_decode($keywords);

        $this->positiveKeywords = $keywords->positiveKeywords;
        $this->negativeKeywords = $keywords->negativeKeywords;
    }

    /**
     * Analyses mood of the given text
     *
     * @param string $text
     * @param string $lang
     * @return int valuation of tweet in percents (can be negative)
     */
    public function analyseMood($text, $lang)
    {
        if (empty($this->positiveKeywords->$lang) || empty($this->negativeKeywords->$lang)) {
            throw new \Exception("There are no keywords for the following language: $lang.");
        }

        $positivePreg = $this->convertToPreg($this->positiveKeywords->$lang);
        $negativePreg = $this->convertToPreg($this->negativeKeywords->$lang);

        // positive or negative tweet valuation, measured in percent
        $valuation = 0;

        $valuation += preg_match_all($positivePreg, $text, $matches) * 25;
        $valuation -= preg_match_all($negativePreg, $text, $matches) * 25;

        if ($valuation > 100) {
            $valuation = 100;
        } elseif ($valuation < -100) {
            $valuation = -100;
        }

        return $valuation;
    }

    /**
     * Converts array to preg string
     * based on self::KEYWORDS_MATCH_PREG pattern
     *
     * @param array $arrayToConvert
     * @return string
     */
    protected function convertToPreg($arrayToConvert)
    {
        $keywordString = "";

        foreach ($arrayToConvert as $value) {
            $keywordString .= $value . "|";
        }

        $keywordString = rtrim($keywordString, "|");

        $preg = str_replace("#keywords#", $keywordString, self::KEYWORD_MATCH_PREG);

        return $preg;
    }
}
