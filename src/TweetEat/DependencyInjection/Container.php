<?php

namespace TweetEat\DependencyInjection;

class Container extends \Pimple
{
    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this['container'] = $this;

        if (!isset($config['root_dir'])) {
            $config['root_dir'] = __DIR__.'/../../..';
        }

        // configure config container
        $this['config'] = $this->share(function ($c) use ($config) {
            $userConfig = parse_ini_file($config['root_dir'].'/app/config/config.ini');
            $config = array_merge($userConfig, $config);
            
            $container = new \Pimple();

            foreach ($config as $parameter => $value) {
                $container[$parameter] = $value;
            }

            return $container;
        });

        // configure services
        $services = parse_ini_file($config['root_dir'].'/app/config/services.ini', true);
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
    }
}