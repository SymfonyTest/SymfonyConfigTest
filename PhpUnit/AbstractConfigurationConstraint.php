<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractConfigurationConstraint extends Constraint
{
    protected $configuration;
    protected $breadcrumbPath;

    public function __construct(ConfigurationInterface $configuration, $breadcrumbPath = null)
    {
        if (is_callable([Constraint::class, '__construct'])) {
            parent::__construct();
        }

        $this->configuration = $configuration;
        $this->breadcrumbPath = $breadcrumbPath;
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
            if (!is_array($values) && null !== $values) {
                throw new \InvalidArgumentException('Configuration values should be an array of arrays');
            }
        }
    }
}
