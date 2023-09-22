<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithRequiredValue implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('root');
        $root = $treeBuilder->getRootNode();
        $root
            ->isRequired()
            ->children()
                ->scalarNode('required_value')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
