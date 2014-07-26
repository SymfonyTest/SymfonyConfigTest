<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use SebastianBergmann\Exporter\Exporter;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

abstract class AbstractConfigurationConstraint extends \PHPUnit_Framework_Constraint
{
    protected $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->exporter = new Exporter();
    }

    protected function processConfiguration(array $configurationValues)
    {
        $processor = new Processor();

        return $processor->processConfiguration($this->configuration, $configurationValues);
    }

    protected function validateConfigurationValuesArray($configurationValues)
    {
        if (!is_array($configurationValues)) {
            throw new \InvalidArgumentException('Configuration values should be an array');
        }

        foreach ($configurationValues as $values) {
            if (!is_array($values)) {
                throw new \InvalidArgumentException('Configuration values should be an array of arrays');
            }
        }
    }
}
