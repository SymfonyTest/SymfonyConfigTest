<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Symfony\Component\Config\Definition\ConfigurationInterface;

class ProcessedConfigurationEqualsConstraint extends AbstractConfigurationConstraint
{
    private $configurationValues;

    public function __construct(ConfigurationInterface $configuration, array $configurationValues)
    {
        $this->validateConfigurationValuesArray($configurationValues);
        $this->configurationValues = $configurationValues;

        parent::__construct($configuration);
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        $processedConfiguration = $this->processConfiguration($this->configurationValues);

        $constraint = new \PHPUnit_Framework_Constraint_IsEqual($other);

        return $constraint->evaluate($processedConfiguration, '', $returnResult);
    }

    public function toString()
    {
        // won't be used, this constraint only wraps \PHPUnit_Framework_Constraint_IsEqual
    }
}
