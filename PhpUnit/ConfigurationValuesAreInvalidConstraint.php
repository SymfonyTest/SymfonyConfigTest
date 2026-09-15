<?php

namespace Matthias\SymfonyConfigTest\PhpUnit;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\ExceptionMessage;
use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use PHPUnit\Framework\Constraint\ExceptionMessageMatchesRegularExpression;
use PHPUnit\Framework\Constraint\ExceptionMessageRegularExpression;
use PHPUnit\Framework\Constraint\MessageIsOrContains;
use PHPUnit\Framework\Constraint\MessageMatchesRegularExpression;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationValuesAreInvalidConstraint extends AbstractConfigurationConstraint
{
    /**
     * @var string|null
     */
    private $expectedMessage;

    /**
     * @var bool
     */
    private $useRegExp;

    /**
     * @param string|null $expectedMessage
     * @param bool        $useRegExp
     * @param string|null $breadcrumbPath
     */
    public function __construct(
        ConfigurationInterface $configuration,
        $expectedMessage = null,
        $useRegExp = false,
        $breadcrumbPath = null
    ) {
        parent::__construct($configuration, $breadcrumbPath);

        $this->expectedMessage = $expectedMessage;
        $this->useRegExp = $useRegExp;
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($other, $description = '', $returnResult = false): ?bool
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

        /** @phpstan-ignore deadCode.unreachable (legacy B/C layer) */
        return null;
    }

    public function toString(): string
    {
        $toString = 'is invalid for the given configuration';

        if ($this->expectedMessage !== null) {
            $toString .= ' (expected exception message: '.$this->expectedMessage.')';
        }

        return $toString;
    }

    private function evaluateException(\Exception $exception, string $description = '', bool $returnResult = false): ?bool
    {
        if ($this->expectedMessage === null) {
            return true;
        }

        return $this->createPhpUnitConstraint()
            ->evaluate($exception->getMessage(), $description, $returnResult);
    }

    private function createPhpUnitConstraint(): Constraint
    {
        if ($this->useRegExp) {
            // Available since PHPUnit 10.0.15
            if (class_exists(ExceptionMessageMatchesRegularExpression::class)) {
                return new ExceptionMessageMatchesRegularExpression($this->expectedMessage);
            }

            // Available between PHPUnit 10.0.0 and 10.0.14 (inclusive)
            if (class_exists(MessageMatchesRegularExpression::class)) {
                /** @phpstan-ignore return.type (legacy B/C layer) */
                return new MessageMatchesRegularExpression('exception', $this->expectedMessage);
            }

            // Available in PHPUnit 9.6
            /** @phpstan-ignore class.notFound,return.type (legacy B/C layer) */
            return new ExceptionMessageRegularExpression($this->expectedMessage);
        }

        // Available since PHPUnit 10.0.15
        if (class_exists(ExceptionMessageIsOrContains::class)) {
            return new ExceptionMessageIsOrContains($this->expectedMessage);
        }

        // Available between PHPUnit 10.0.0 and 10.0.14 (inclusive)
        if (class_exists(MessageIsOrContains::class)) {
            /** @phpstan-ignore return.type (legacy B/C layer) */
            return new MessageIsOrContains('exception', $this->expectedMessage);
        }

        // Available in PHPUnit 9.6
        /** @phpstan-ignore class.notFound,return.type (legacy B/C layer) */
        return new ExceptionMessage($this->expectedMessage);
    }
}
