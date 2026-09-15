<?php

namespace Matthias\SymfonyConfigTest\Partial;

use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

class PartialProcessor
{
    /**
     * @param string|null $breadcrumbPath
     *
     * @return array
     */
    public function process(ArrayNode $node, $breadcrumbPath, array $configs)
    {
        PartialNode::excludeEverythingNotInBreadcrumbPath($node, $breadcrumbPath);

        return (new Processor())->process($node, $configs);
    }

    /**
     * @param string|null $breadcrumbPath
     *
     * @return array
     */
    public function processConfiguration(ConfigurationInterface $configuration, $breadcrumbPath, array $configs)
    {
        return $this->process($configuration->getConfigTreeBuilder()->buildTree(), $breadcrumbPath, $configs);
    }
}
