<?php

namespace As3\Bundle\PostProcessBundle\Tests;

use As3\Bundle\PostProcessBundle\Task\TaskManager;

class TaskManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    protected function setUp()
    {
        $this->manager = new TaskManager();
    }

    public function testTaskPriority()
    {
        $task1 = new TestTask('1');
        $task2 = new TestTask('2');
        $task3 = new TestTask('3');

        $this->manager->addTask($task1);
        $this->manager->addTask($task2, 5);
        $this->manager->addTask($task3, 1);

        $tasks = $this->manager->getTasks();
        $this->assertEquals(3, count($tasks));
        $this->assertEquals('2', $tasks[0]->getKey(), 'Task priority order is incorrect.');
        $this->assertEquals('3', $tasks[1]->getKey(), 'Task priority order is incorrect.');
    }
}
