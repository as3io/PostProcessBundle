<?php
namespace As3\PostProcessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use As3\PostProcessBundle\DependencyInjection\Compiler;

class As3PostProcessBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new Compiler\AddTasksCompilerPass());
        $container->addCompilerPass(new Compiler\AddPluginsCompilerPass());
    }
}
