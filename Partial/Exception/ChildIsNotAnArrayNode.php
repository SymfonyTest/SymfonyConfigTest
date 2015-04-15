<?php

namespace Matthias\SymfonyConfigTest\Partial\Exception;

use Symfony\Component\Config\Definition\BaseNode;

class ChildIsNotAnArrayNode extends InvalidNodeNavigation
{
    public function __construct(BaseNode $parentNode, $nodeName)
    {
        parent::__construct(
            sprintf(
                'Child node "%s" is not an array node (current path: "%s")',
                $nodeName,
                $parentNode->getPath()
            )
        );
    }
}
