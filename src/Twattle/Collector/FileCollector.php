<?php

namespace Twattle\Collector;

class FileCollector
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path = null)
    {
        $this->path = $path;
    }

    /**
     * @param string $data
     */
    public function process($data)
    {
        file_put_contents($this->getPath(), (string)$data, FILE_APPEND | LOCK_EX);
    }

    /**
     * @param type $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
