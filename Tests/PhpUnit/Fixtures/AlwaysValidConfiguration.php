<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AlwaysValidConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('root');
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $treeBuilder->root('root');
        }

        return $treeBuilder;
    }
}
