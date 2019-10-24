<?php

namespace Matthias\SymfonyConfigTest\Tests\Partial\Fixtures;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationStub implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('root');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $root = $treeBuilder->getRootNode();
        } else {
            $root = $treeBuilder->root('root');
        }
        $root
            ->children()
                ->arrayNode('only_test_this_node')
                    ->children()
                        ->scalarNode('scalar_node')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ignore_this_node')
                    // this would normally trigger an error
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
