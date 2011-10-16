<?php

namespace TweetEat\DependencyInjection;

class Container extends \Pimple
{
    /**
     * @param array $parameters
     */
    public function __construct($parameters = array())
    {
        // reference container for possibility to inject services with it
        $this['container'] = $this;

        // set root directory
        if (!isset($parameters['sys_root_dir'])) {
            $parameters['root_dir'] = __DIR__.'/../../..';
        }

        // import parameters from config
        $config = parse_ini_file($parameters['sys_root_dir'].'/app/config/config.ini', true);
        foreach ($config as $domain => $params) {
            if (is_array($params)) {
                foreach ($params as $name => $value) {
                    $this[$domain.'_'.$name] = $value;
                }
            }
            else {
                $this[$domain] = $params;
            }
        }

        // import parameters from arguments
        foreach ($parameters as $name => $value) {
            $this[$name] = $value;
        }

        // import services from config
        $services = parse_ini_file($parameters['sys_root_dir'].'/app/config/services.ini', true);
        foreach ($services as $name => $info) {
            $callback = function ($c) use ($info) {
                if (isset($info['arguments'])) {
                    foreach ($info['arguments'] as $key => $argument) {
                        $length = strlen($argument);
                        if ($length > 2 && $argument[0] == '%' && $argument[$length-1] == '%') {
                            $info['arguments'][$key] = $c[substr($argument, 1, $length-2)];
                        }
                    }

                    $r = new \ReflectionClass($info['class']);
                    return $r->newInstanceArgs($info['arguments']);
                }
                else {
                    return new $info['class'];
                }
            };

            if (isset($info['shared']) && $info['shared']) {
                $callback = $this->share($callback);
            }

            $this[$name] = $callback;
        }

        // register mongodb
        $this['mongodb'] = $this->share(function ($c) {
            $database = $c['mongodb_database'];
            $username = $c['mongodb_username'];
            $password = $c['mongodb_password'];

            $options = array();

            if ('' !== $username) {
                $options['username'] = $username;
                $options['password'] = $password;
            }
            
            $conn = new \Mongo($c['mongodb_server'], $options);
            return $conn->$database;
        });
    }
}