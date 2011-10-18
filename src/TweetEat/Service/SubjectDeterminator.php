<?php

namespace TweetEat\Service;

class SubjectDeterminator implements ServiceInterface
{
    /**
     * @var \Pimple
     */
    protected $container;

    /**
     * @param \Pimple $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Runs the service
     */
    public function run()
    {
        $database = $this->container['database'];

        $products = iterator_to_array($database->getProductCollection()->findAll());

        do {
            $tweets = $database->getTweetCollection()->findWithoutSubject();

            foreach ($tweets as $tweet) {
                if (!isset($tweet['original']['text'])) {
                    continue;
                }

                foreach ($products as $product) {
                    $matched = false;
                    foreach ($product['keywords'] as $keyword) {
                        if (stripos($tweet['original']['text'], $keyword) !== false) {
                            $matched = true;
                            break;
                        }
                    }

                    if ($matched) {
                        $subject = array(
                            '_id' => $product['_id'],
                            'type' => 'product',
                        );

                        $tweet['subjects'][] = $subject;

                        $database->getTweetCollection()->addSubject($tweet['_id'], $subject);
                    }
                }

                if (!isset($tweet['subjects'])) {
                    $database->getTweetCollection()->remove($tweet['_id']);
                }
            }

            sleep(1);
        } while (true);
    }
}