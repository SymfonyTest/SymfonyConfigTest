# SymfonyConfigTest

*By Matthias Noback*

[![Build Status](https://secure.travis-ci.org/matthiasnoback/SymfonyConfigTest.png)](http://travis-ci.org/matthiasnoback/SymfonyConfigTest)

Writing configuration classes using the [Symfony Config
Component](http://symfony.com/doc/current/components/config/definition.html) can be quite hard. To help you verify the
validity of the resulting config node tree, this library provides a PHPUnit test case and some custom assertions.

## Installation

Using Composer:

    php composer.phar require --dev matthiasnoback/symfony-config-test 0.*

## Usage

Create a test case and use the trait from ``Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait``.
Then implement ``getConfiguration()``:

```php
<?php

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
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
    public function getConfigTreeBuilder()
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

When you provide an empty array as the values for this configuration, you would expect an exception since the
``required_value`` node is required. You can assert that a given set of configuration values is invalid using the
``assertConfigurationIsInvalid()`` method:

```php
<?php

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    public function testValuesAreInvalidIfRequiredValueIsNotProvided()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array() // no values at all
            ),
            'required_value' // (part of) the expected exception message - optional
        );
    }
}
```

### Test processed configuration values

You may also want to verify that after processing an array of configuration values the result will be as expected:

```php
<?php

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    public function testProcessedValueContainsRequiredValue()
    {
        $this->assertProcessedConfigurationEquals(array(
            array('required_value' => 'first value'),
            array('required_value' => 'last value')
        ), array(
            'required_value'=> 'last value'
        ));
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
    public function getConfigTreeBuilder()
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
public function processed_configuration_for_array_node_1()
{
    $this->assertProcessedConfigurationEquals(
        array(
            array('array_node_1' => array('required_value_1' => 'original value'),
            array('array_node_1' => array('required_value_1' => 'final value')
        ),
        array(
            'array_node_1' => array(
                'required_value_1' => 'final value'
            )
        ),
        // the path of the nodes you want to focus on in this test:
        'array_node_1'
    );
}
```

This would trigger no validation errors for any value in the `array_node_2` branch.

Note that the `$breadcrumbPath` can be even more specific, e.g. `"doctrine.orm"` (which would skip configuration
processing for branch `"doctrine.dbal"`, etc.).
