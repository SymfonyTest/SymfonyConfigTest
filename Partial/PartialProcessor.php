<?php

namespace Matthias\SymfonyConfigTest\Partial;

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class PartialProcessor
{
    public function process(ArrayNode $node, $breadcrumbPath, array $configs)
    {
        PartialNode::excludeEverythingNotInBreadcrumbPath($node, $breadcrumbPath);

        $processor = new Processor();

        return $processor->process($node, $configs);
    }

    public function processConfiguration(ConfigurationInterface $configuration, $breadcrumbPath, array $configs)
    {
        return $this->process($configuration->getConfigTreeBuilder()->buildTree(), $breadcrumbPath, $configs);
    }
}
