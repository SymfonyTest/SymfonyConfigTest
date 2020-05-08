<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreValidConstraint extends AbstractConfigurationConstraint
{
    public function __construct(ConfigurationInterface $configuration, $breadcrumbPath = null)
    {
        parent::__construct($configuration, $breadcrumbPath);
    }

    public function evaluate($other, $description = '', $returnResult = false): ?bool
    {
        $this->validateConfigurationValuesArray($other);

        $success = true;

        try {
            $this->processConfiguration($other);
        } catch (InvalidConfigurationException $exception) {
            $success = false;
            $description = empty($description) ? $exception->getMessage() : $description."\n".$exception->getMessage();
        }

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $this->fail($other, $description);
        }

        return null;
    }

    public function toString(): string
    {
        return 'is valid for the given configuration';
    }
}
