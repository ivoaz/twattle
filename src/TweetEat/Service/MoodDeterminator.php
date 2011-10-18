<?php

namespace TweetEat\Service;

class MoodDeterminator implements ServiceInterface
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
        $collection = $database->getTweetCollection();

        do {
            $tweets = $collection->findWithoutMood();

            foreach ($tweets as $tweet) {
                foreach ($tweet['subjects'] as $subject) {
                    $subject['mood'] = array(
                        'value' => rand(-1, 1),
                    );
                    $collection->updateMood($tweet['_id'], $subject['_id'], $subject['mood']['value']);
                }
            }

            sleep(1);
        } while (true);
    }
}