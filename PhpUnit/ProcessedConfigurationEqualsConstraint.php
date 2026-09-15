<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\IsEqual;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ProcessedConfigurationEqualsConstraint extends AbstractConfigurationConstraint
{
    private array $configurationValues;

    /**
     * @param string|null $breadcrumbPath
     */
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

        return (new IsEqual($other))->evaluate($processedConfiguration, '', $returnResult);
    }

    public function toString(): string
    {
        // won't be used, this constraint only wraps IsEqual
        return '';
    }
}
