<?php

namespace Matthias\SymfonyConfigTest\Partial\Exception;

use Symfony\Component\Config\Definition\NodeInterface;

class UndefinedChildNode extends InvalidNodeNavigation
{
    public function __construct(NodeInterface $parentNode, $childNodeName)
    {
        parent::__construct(
            sprintf(
                'Undefined child node "%s" (the part of the path that was successful: "%s")',
                $childNodeName,
                $parentNode->getPath()
            )
        );
    }
}
