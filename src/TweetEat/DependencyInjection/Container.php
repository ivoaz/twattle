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
    }

    /**
     * @return \TweetEat\Database\Mongo
     */
    public function getDatabase()
    {
        return $this->offsetGet('database');
    }
}
