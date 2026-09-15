<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\Config\Definition\ConfigurationInterface;

abstract class AbstractConfigurationConstraint extends Constraint
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var string|null
     */
    protected $breadcrumbPath;

    /**
     * @param string|null $breadcrumbPath
     */
    public function __construct(ConfigurationInterface $configuration, $breadcrumbPath = null)
    {
        $this->configuration = $configuration;
        $this->breadcrumbPath = $breadcrumbPath;
    }

    /**
     * @return array
     */
    protected function processConfiguration(array $configurationValues)
    {
        return (new PartialProcessor())->processConfiguration($this->configuration, $this->breadcrumbPath, $configurationValues);
    }

    /**
     * @param mixed $configurationValues
     *
     * @return void
     *
     * @throws \InvalidArgumentException if the configuration values structure is not the required shape
     */
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
