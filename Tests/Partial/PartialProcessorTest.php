<?php

namespace Matthias\SymfonyConfigTest\Tests\Partial;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\Tests\Partial\Fixtures\ConfigurationStub;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class PartialProcessorTest extends TestCase
{
    #[Test]
    public function it_processes_only_the_values_in_the_breadcrumb_path_for_a_given_node()
    {
        $treeBuilder = new TreeBuilder('root');
        $root = $treeBuilder->getRootNode();
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
        $node = $treeBuilder->buildTree();

        $partialProcessor = new PartialProcessor();

        $processedConfig = $partialProcessor->process($node, 'only_test_this_node', [
            [
                'only_test_this_node' => [
                    'scalar_node' => 'no',
                ],
            ],
            [
                'only_test_this_node' => [
                    'scalar_node' => 'yes',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'only_test_this_node' => [
                    'scalar_node' => 'yes',
                ],
            ],
            $processedConfig
        );
    }

    #[Test]
    public function it_processes_only_the_values_in_the_given_breadcrumb_path_for_a_given_configuration_instance()
    {
        $partialProcessor = new PartialProcessor();

        $processedConfig = $partialProcessor->processConfiguration(
            new ConfigurationStub(),
            'only_test_this_node',
            [
                [
                    'only_test_this_node' => [
                        'scalar_node' => 'yes',
                    ],
                ],
            ]
        );

        $this->assertSame(
            [
                'only_test_this_node' => [
                    'scalar_node' => 'yes',
                ],
            ],
            $processedConfig
        );
    }
}
