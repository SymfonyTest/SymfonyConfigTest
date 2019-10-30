<?php

namespace Matthias\SymfonyConfigTest\Tests\PhpUnit\Fixtures;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithRequiredValue implements ConfigurationInterface
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
            ->isRequired()
            ->children()
                ->scalarNode('required_value')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
