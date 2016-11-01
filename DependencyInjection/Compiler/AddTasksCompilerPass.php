<?php
namespace As3\PostProcessBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddTasksCompilerPass implements CompilerPassInterface
{
    /**
     * Adds tagged autoloaders to the manager service
     *
     * @param   ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $managerId = 'as3_post_process.task.manager';
        if (!$container->hasDefinition($managerId)) {
            return;
        }
        // Get the manager service definition
        $definition = $container->getDefinition($managerId);

        // Get the tagged tasks
        $tag   = 'as3_post_process.task';
        $tasks = $container->findTaggedServiceIds($tag);

        foreach ($tasks as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                // Add the task to the manager service definition
                $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
                $definition->addMethodCall(
                    'addTask',
                    [new Reference($id), $priority]
                );
            }
        }
    }
}
