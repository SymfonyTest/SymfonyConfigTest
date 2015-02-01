<?php

namespace Matthias\SymfonyConfigTest\Partial\Exception;

use Symfony\Component\Config\Definition\BaseNode;

abstract class InvalidNodeNavigation extends \LogicException
{
    protected static function renderTravelledPath(BaseNode $node)
    {
        if ($node->getParent()) {
            $prefix = self::renderTravelledPath($node->getParent());
            $prefix .= '.';
        } else {
            $prefix = '';
        }

        return $prefix . $node->getName();
    }
}
