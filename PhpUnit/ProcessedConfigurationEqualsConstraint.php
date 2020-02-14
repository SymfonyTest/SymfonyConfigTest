<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\IsEqual;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ProcessedConfigurationEqualsConstraint extends AbstractConfigurationConstraint
{
    private $configurationValues;

    public function __construct(
        ConfigurationInterface $configuration,
        array $configurationValues,
        $breadcrumbPath = null
    ) {
        $this->validateConfigurationValuesArray($configurationValues);
        $this->configurationValues = $configurationValues;

        parent::__construct($configuration, $breadcrumbPath);
    }

    public function evaluate($other, $description = '', $returnResult = false): ?bool
    {
        $processedConfiguration = $this->processConfiguration($this->configurationValues);

        $constraint = new IsEqual($other);

        return $constraint->evaluate($processedConfiguration, '', $returnResult);
    }

    public function toString(): string
    {
        // won't be used, this constraint only wraps IsEqual
        return '';
    }
}
