<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreValidConstraint extends AbstractConfigurationConstraint
{
    public function __construct(ConfigurationInterface $configuration)
    {
        parent::__construct($configuration);
    }

    public function matches($other)
    {
        $this->validateConfigurationValuesArray($other);

        $success = true;

        try {
            $this->processConfiguration($other);
        } catch (InvalidConfigurationException $exception) {
            $success = false;
        }

        return $success;
    }

    public function toString()
    {
        return 'is valid for the given configuration';
    }
}
