<?php

namespace As3\Bundle\PostProcessBundle\Task;

/**
 * Interface for Tasks to be executed "post process" - after the response is sent
 *
 * @author Jacob Bare <jacob.bare@gmail.com>
 */
interface TaskInterface
{
    /**
     * Runs the task/code
     *
     * @return self
     */
    public function run();
}
