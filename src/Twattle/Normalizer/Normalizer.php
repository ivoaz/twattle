<?php

namespace Twattle\Normalizer;

class Normalizer
{
    public function normalize($text)
    {
        // replace retweets
        $text = preg_replace('/RT @([a-zA-Z0-9_]+):/', "RETWEAT", $text);

        // replace usernames
        $text = preg_replace('/@([a-zA-Z0-9_]+)/', "USERNAME", $text);

        // replaces tags
        $text = preg_replace('/#([a-zA-Z0-9_]+)/', "$1", $text);

        // replace urls
        $text = preg_replace('/(http|https|ftp|ftps):\/\/([^\s]+)/', "URL", $text);

        return $text;
    }
}
