# Symfony Config Test

*By Matthias Noback and contributors*

[![Build Status](https://secure.travis-ci.org/SymfonyTest/SymfonyConfigTest.png)](http://travis-ci.org/SymfonyTest/SymfonyConfigTest)

Writing configuration classes using the [Symfony Config
Component](http://symfony.com/doc/current/components/config/definition.html) can be quite hard. To help you verify the
validity of the resulting config node tree, this library provides a PHPUnit test case and some custom assertions.

## Installation

Using Composer:

    $ composer require --dev matthiasnoback/symfony-config-test

## Usage

Create a test case and use the trait from ``Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait``.
Then implement ``getConfiguration()``:

```php
<?php

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use App\Configuration;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
```

### Test invalid configuration values

Let's assume the ``Configuration`` class you want to test looks like this:

```php
<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithRequiredValue implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('root');
        $rootNode
            ->isRequired()
            ->children()
                ->scalarNode('required_value')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
```

When you provide an empty array as the value for this configuration, you would expect an exception since the
``required_value`` node is required. You can assert that a given set of configuration values is invalid using the
``assertConfigurationIsInvalid()`` method:

```php
<?php

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function values_are_invalid_if_required_value_is_not_provided(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                [] // no values at all
            ],
            'required_value' // (part of) the expected exception message - optional
        );
    }
}
```

### Test processed configuration values

You may also want to verify that after processing an array of configuration values the result will be as expected:

```php
<?php

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function processed_value_contains_required_value(): void
    {
        $this->assertProcessedConfigurationEquals([
            ['required_value' => 'first value'],
            ['required_value' => 'last value']
        ], [
            'required_value'=> 'last value'
        ]);
    }
}
```

Please note: the first argument of each of the ``assert*`` methods is an *array of arrays*. The extra nesting level
allows you to test the merge process. See also the section [Merging
options](http://symfony.com/doc/current/components/config/definition.html#merging-options) of the Config Component
documentation.

### Test a subset of the configuration tree

Using this library it's possible to test just one branch of your configuration tree. Consider the following node tree
definition, which contains the branches `array_node_1` and `array_node_2`:

```php
<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithTwoBranches implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('root');
        $rootNode
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
```

If you want to test, for instance, only the `array_node_1` branch from the example below, and ignore the `array_node_2`,
provide `array_node_1` as the argument for the `$breadcrumbPath` parameter of the test helper functions, for example:

```php
/**
 * @test
 */
public function processed_configuration_for_array_node_1(): void
{
    $this->assertProcessedConfigurationEquals(
        array(
            ['array_node_1' => ['required_value_1' => 'original value']],
            ['array_node_1' => ['required_value_1' => 'final value']]
        ),
        [
            'array_node_1' => [
                'required_value_1' => 'final value'
            ]
        ],
        // the path of the nodes you want to focus on in this test:
        'array_node_1'
    );
}
```

This would trigger no validation errors for any value in the `array_node_2` branch.

Note that the `$breadcrumbPath` can be even more specific, e.g. `"doctrine.orm"` (which would skip configuration
processing for branch `"doctrine.dbal"`, etc.).

Also note that you can only traverse over array nodes using the `.` in the breadcrumb path. The last part of the breadcrumb path can be any other type of node.

#### Test a subset of the prototyped configuration tree

You can traverse through prototype array nodes using `*` as its name in the breadcrumb path.

```php
<?php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PrototypedConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('root');
        $rootNode
            ->children()
                ->arrayNode('array_node')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('default_value')->cannotBeEmpty()->defaultValue('foobar')->end()
                            ->scalarNode('required_value')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
```

If you want to test whether `default_value` is set to `foobar` by default, but don't want the test to be affected by
requirements on `required_value` node, you can define its path as `array_node.*.default_value`, for example:

```php
/**
 * @test
 */
public function processed_configuration_for_array_node_1(): void
{
    $this->assertProcessedConfigurationEquals(
        [
            ['array_node' => ['prototype_name' => null]],
        ],
        [
            'array_node' => [
                'prototype_name' => [
                    'default_value' => 'foobar'
                ]
            ]
        ],
        // the path of the nodes you want to focus on in this test:
        'array_node.*.default_value'
    );
}
```

## Version Guidance

| Version | Released     | PHPUnit             | Status     |
|---------|--------------| --------------------|------------|
| 4.x     | Mar 5, 2018  | 7.x and 8.x and 9.x | Latest     |
| 3.x     | Nov 30, 2017 | 6.x                 | Bugfixes   |
| 2.x     | Jun 18, 2016 | 4.x and 5.x         | EOL        |
| 1.x     | Oct 12, 2014 | 3.x                 | EOL        |
