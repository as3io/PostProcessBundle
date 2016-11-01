<?php

namespace As3\Bundle\PostProcessBundle\Tests;

use As3\Bundle\PostProcessBundle\Task\TaskInterface;

class TestTask implements TaskInterface
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function run()
    {
    }
}
