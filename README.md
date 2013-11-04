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

Create a test case and extend from ``Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase``. Then implement
``getConfiguration()``:

```php
<?php

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }
}
```

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

class ConfigurationTest extends AbstractConfigurationTestCase
{
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

You may also want to verify that after processing an array of configuration values the result will be as expected:

```php
<?php

class ConfigurationTest extends AbstractConfigurationTestCase
{
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
