<?php
namespace As3\Bundle\PostProcessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use As3\Bundle\PostProcessBundle\DependencyInjection\Compiler;

class As3PostProcessBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new Compiler\AddTasksCompilerPass());
        $container->addCompilerPass(new Compiler\AddPluginsCompilerPass());
    }
}
