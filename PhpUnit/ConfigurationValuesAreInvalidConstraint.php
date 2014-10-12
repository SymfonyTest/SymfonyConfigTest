<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreInvalidConstraint extends AbstractConfigurationConstraint
{
    private $expectedMessage;
    private $useRegExp;

    public function __construct(ConfigurationInterface $configuration, $expectedMessage = null, $useRegExp = false)
    {
        parent::__construct($configuration);

        $this->expectedMessage = $expectedMessage;
        $this->useRegExp = $useRegExp;
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        $this->validateConfigurationValuesArray($other);

        try {
            $this->processConfiguration($other);
        } catch (InvalidConfigurationException $exception) {
            return $this->evaluateException($exception, $description, $returnResult);
        }

        if ($returnResult) {
            return false;
        }

        $this->fail($other, $description);
    }

    public function toString()
    {
        $toString = 'is invalid for the given configuration';

        if ($this->expectedMessage !== null) {
            $toString .= ' (expected exception message: '.$this->expectedMessage.')';
        }

        return $toString;
    }

    private function evaluateException(\Exception $exception, $description, $returnResult)
    {
        if ($this->expectedMessage === null) {
            return true;
        }

        return $this->createPhpUnitConstraint()
            ->evaluate($exception, $description, $returnResult);
    }

    private function createPhpUnitConstraint()
    {
        // Matching by regular expression was added in PHPUnit 4.2.0
        if ($this->useRegExp && version_compare(\PHPUnit_Runner_Version::id(), '4.2.0', '<')) {
            throw new \InvalidArgumentException('Currently installed PHPUnit version does not support matching exception messages by regular expression.');
        }

        // Matching by regular expression was moved to a separate constraint in PHPUnit 4.3.0
        if ($this->useRegExp && class_exists('PHPUnit_Framework_Constraint_ExceptionMessageRegExp')) {
            return new \PHPUnit_Framework_Constraint_ExceptionMessageRegExp($this->expectedMessage);
        }

        return new \PHPUnit_Framework_Constraint_ExceptionMessage($this->expectedMessage);
    }
}
