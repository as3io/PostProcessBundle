<?php

namespace As3\PostProcessBundle\Plugins;

use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines structure for PostProcess plugins
 *
 * @author Josh Worden <solocommand@gmail.com>
 */
interface PluginInterface
{
    /**
     * Fires before TaskManager registers shutdown functions for loaded tasks.
     * Called by As3\PostProcessBundle\Task\TaskManager::execute()
     *
     * @param   PostResponseEvent   $event  The Symfony post response event
     */
    public function execute(PostResponseEvent $event);

    /**
     * Handles modification (filtering) of the response as needed.
     * Called by As3\PostProcessBundle\Task\TaskManager::filterResponse() before returning the response.
     *
     * @param   Response            $event  The Symfony Response
     */
    public function filterResponse(Response $response);
}
