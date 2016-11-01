<?php
namespace As3\PostProcessBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddPluginsCompilerPass implements CompilerPassInterface
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

        // Get the tagged plugins
        $tag     = 'as3_post_process.plugin';
        $plugins = $container->findTaggedServiceIds($tag);

        foreach ($plugins as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                // Add the plugin to the manager service definition
                $definition->addMethodCall(
                    'addPlugin',
                    [new Reference($id)]
                );
            }
        }
    }
}
