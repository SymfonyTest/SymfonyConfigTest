<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithMultipleArrayKeys implements ConfigurationInterface
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
                ->arrayNode('array_node_1')
                    ->isRequired()
                    ->children()
                        ->scalarNode('required_value_1')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('array_node_2')
                    ->isRequired()
                    ->children()
                        ->scalarNode('required_value_2')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
