<?php

namespace As3\Bundle\PostProcessBundle\Task;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use As3\Bundle\PostProcessBundle\Plugins\PluginInterface;

/**
 * The task manager is responsible for running PHP code after a response is sent to the browser.
 * This facilitates executing resource and/or time itensive processes without the user having to wait for completion.
 * Code is executed via TaskInterface implementations, and can be prioritized.
 *
 * @author Jacob Bare <jacob.bare@gmail.com>
 * @author Josh Worden <solocommand@gmail.com>
 */
class TaskManager
{
    /**
     * All registered tasks to run on shutdown, after the response is sent.
     *
     * @see TaskManager::addTask() To add a TaskInterface class
     * @var TaskInterface[]
     */
    private $tasks = [];

    /**
     * All enabled plugins to be executed on kernel.response and kernel.terminate events.
     *
     * @see TaskManager::addPlugin()
     * @var PluginInterface[]
     */
    private $plugins = [];

    /**
     * @var boolean
     */
    private $masterRequest = true;

    /**
     * Determines whether Tasks should be executed
     *
     * @var bool
     */
    private $enabled = true;

    /**
     * Adds a registered task.
     * Tasks can be added by:
     * 1. Tagging TaskInterface services through DI.
     * 2. Implementing a TaskInterface class and mannually adding it
     *
     * All tasks must be registered before Response::send() is called by Symfony.
     *
     * @param  TaskInterface    $task       The task to run
     * @param  int              $priority   The priority: a higher value runs first. Default 0.
     * @return self
     */
    public function addTask(TaskInterface $task, $priority = 0)
    {
        $prioritized = [
            'object'    => $task,
            'priority'  => (Integer) $priority,
        ];
        $this->tasks[] = $prioritized;

        $sortFunc = function ($a, $b) {
            return $a['priority'] > $b['priority'] ? -1 : 1;
        };
        uasort($this->tasks, $sortFunc);
        return $this;
    }

    /**
     * Adds an enabled plugin to be utilized in relevant methods below.
     *
     * @see TaskManager::execute()
     * @see TaskManager::filterResponse()
     *
     * @param PluginInterface   $plugin     The plugin to add
     * @return self
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
        return $this;
    }

    /**
     * Gets all registered tasks
     *
     * @return TaskInterface[]
     */
    public function getTasks()
    {
        $tasks = [];
        foreach ($this->tasks as $task) {
            $tasks[] = $task['object'];
        }
        return $tasks;
    }

    /**
     * Gets all registered tasks
     *
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Determines if tasks are registered
     *
     * @return bool
     */
    public function hasTasks()
    {
        $tasks = $this->getTasks();
        return !empty($tasks);
    }

    /**
     * Determines if plugins are registered
     *
     * @return bool
     */
    public function hasPlugins()
    {
        $plugins = $this->getPlugins();
        return !empty($plugins);
    }

    /**
     * Enables the TaskManager for executing Tasks
     *
     * @return self
     */
    public function enable()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Disables the TaskManager from executing Tasks
     *
     * @return self
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * Determines if the TaskManager is enabled for executing Tasks
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true === $this->enabled;
    }

    /**
     * Executes the post-response Tasks.
     * Is called via Symfony's kernel.terminate event.
     *
     * @param   PostResponseEvent   $event
     */
    public function execute(PostResponseEvent $event)
    {
        if ($this->isEnabled() && $this->masterRequest) {
            $session = $event->getRequest()->getSession();
            if (null !== $session && $session->isStarted()) {
                // Saves the session, which calls PHP's session_write_close()
                $session->save();
            }

            // Allow any loaded plugins to fire
            foreach ($this->getPlugins() as $plugin) {
                $plugin->execute($event);
            }

            foreach ($this->getTasks() as $task) {
                $task->run();
            }
        }
    }

    /**
     * Sets the HTTP headers required to execute post-response Tasks.
     * Is called via Symfony's kernel.response event.
     *
     * @param   FilterResponseEvent     $event
     * @return  Response
     */
    public function filterResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (!$event->isMasterRequest()) {
            // Return the regular response
            $this->masterRequest = false;
            return $response;
        }
        $this->masterRequest = true;

        if ((!$this->hasTasks() && !$this->hasPlugins()) || !$this->isEnabled()) {
            // Nothing to process. Return response.
            return $response;
        }

        ignore_user_abort(true);

        // Allow any loaded plugins to fire
        foreach ($this->getPlugins() as $plugin) {
            $plugin->filterResponse($response);
        }

        $content = $response->getContent();
        $length = strlen($response->getContent());

        $headers = [
            'connection'        => 'close',
            'content-length'    => $length,
        ];

        // Ensures minimum output has been set, otherwise PHP will not flush properly and tasks will hang the browser.
        $minOutputLen = PHP_INT_SIZE * 1024;
        if ($length < $minOutputLen && !$response->isEmpty()) {
            $padding = $minOutputLen - $length + 1;
            $response->setContent($content.str_pad('', $padding));
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        return $response;
    }
}
