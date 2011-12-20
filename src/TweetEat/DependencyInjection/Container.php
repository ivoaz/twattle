<?php

namespace TweetEat\DependencyInjection;

class Container extends \Pimple
{
    /**
     * @param array $parameters
     */
    public function __construct($parameters = array())
    {
        // set root directory
        if (!isset($parameters['sys.root_dir'])) {
            $parameters['sys.root_dir'] = __DIR__.'/../../..';
        }

        // import parameters from config
        $config = parse_ini_file($parameters['sys.root_dir'].'/app/config/config.ini', true);
        foreach ($config as $domain => $param) {
            if (is_array($param)) {
                foreach ($param as $name => $value) {
                    $this[$domain.'.'.$name] = $value;
                }
            }
            else {
                $this[$domain] = $param;
            }
        }

        // import parameters from arguments
        foreach ($parameters as $name => $value) {
            $this[$name] = $value;
        }

        // register mongodb
        $this['mongodb'] = $this->share(function ($c) {
            $database = $c['mongodb.database'];
            $username = $c['mongodb.username'];
            $password = $c['mongodb.password'];

            $options = array();

            if ('' !== $username) {
                $options['username'] = $username;
                $options['password'] = $password;
            }
            
            $conn = new \Mongo($c['mongodb.server'], $options);
            
            return $conn->$database;
        });

        // register database
        $this['database'] = $this->share(function ($c) {
            return new \TweetEat\Database\Mongo($c['mongodb']);
        });
        
        // register normalizer
        $this['normalizer'] = $this->share(function ($c) {
            return new \TweetEat\Normalizer\Normalizer();
        });
        
        // register object determinator
        $this['object_determinator'] = $this->share(function ($c) {
            return new \TweetEat\Determinator\ObjectDeterminator($c->getDatabase()->getObjectCollection());
        });
        
        // register sentiment analyser
        $this['sentiment_analyser'] = $this->share(function ($c) {
            return new \TweetEat\Analyser\KeywordSentimentAnalyser($c->getDatabase()->getLexiconCollection());
        });

        // register processor
        $this['processor'] = $this->share(function ($c) {
            return new \TweetEat\Processor\Processor($c['normalizer'], $c['object_determinator'], $c['sentiment_analyser'], $c['naive_bayesian']);
        });

        // register collector
        $this['collector'] = $this->share(function ($c) {
            return new \TweetEat\Collector\MongoCollector($c->getDatabase()->getTweetCollection());
        });

        // register streamline
        $this['streamline'] = $this->share(function ($c) {
            return new \TweetEat\Streamline\FilterStreamline($c['twitter.api_username'], $c['twitter.api_password'], $c['collector']);
        });

        // register naive bayesian
        $this['naive_bayesian'] = $this->share(function ($c) {
            return new \DotClear\Weblog\NaiveBayesian($c['naive_bayesian.storage']);
        });

        // register naive bayesian storage
        $this['naive_bayesian.storage'] = $this->share(function ($c) {
            return new \DotClear\Weblog\NaiveBayesianStorage($c['mysql.username'], $c['mysql.password'], $c['mysql.server'], $c['mysql.database']);
        });
    }

    /**
     * @return TweetEat\Database\Mongo
     */
    public function getDatabase()
    {
        return $this->offsetGet('database');
    }

    /**
     * @return TweetEat\Processor\Processor
     */
    public function getProcessor()
    {
        return $this->offsetGet('processor');
    }

    /**
     * @return TweetEat\Streamline\FilterStreamline
     */
    public function getStreamline()
    {
        return $this->offsetGet('streamline');
    }
}
