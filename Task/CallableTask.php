<?php

namespace As3\Bundle\PostProcessBundle\Task;

/**
 * Task for running a callable property.
 *
 * @author Jacob Bare <jacob.bare@gmail.com>
 */
class CallableTask implements TaskInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param   callable    $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $callable = $this->callable;
        $callable();
    }
}
