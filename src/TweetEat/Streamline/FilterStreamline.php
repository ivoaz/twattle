<?php

namespace TweetEat\Streamline;

class FilterStreamline extends \Phirehose
{
    protected $processor;

    /**
     * @param string $username
     * @param string $password
     * @param mixed $processor
     */
    public function __construct($username, $password, $processor)
    {
        parent::__construct($username, $password, \Phirehose::METHOD_FILTER);
        $this->processor = $processor;
    }

    /**
     * @param type $status
     */
    public function enqueueStatus($status)
    {
        $this->processor->process($status);
    }
}