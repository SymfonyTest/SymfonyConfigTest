<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use SebastianBergmann\Exporter\Exporter;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractConfigurationConstraint extends \PHPUnit_Framework_Constraint
{
    protected $configuration;
    protected $breadcrumbPath;

    public function __construct(ConfigurationInterface $configuration, $breadcrumbPath = null)
    {
        $this->configuration = $configuration;
        $this->breadcrumbPath = $breadcrumbPath;
        $this->exporter = new Exporter();
    }

    protected function processConfiguration(array $configurationValues)
    {
        $processor = new PartialProcessor();

        return $processor->processConfiguration($this->configuration, $this->breadcrumbPath, $configurationValues);
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
