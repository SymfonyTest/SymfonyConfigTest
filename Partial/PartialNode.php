<?php

namespace Matthias\SymfonyConfigTest\Partial;

use Matthias\SymfonyConfigTest\Partial\Exception\ChildIsNotAnArrayNode;
use Matthias\SymfonyConfigTest\Partial\Exception\UndefinedChildNode;
use Symfony\Component\Config\Definition\ArrayNode;

class PartialNode
{
    private static $nodeChildrenProperty;

    /**
     * Provide an ArrayNode instance (e.g. the root node created by a TreeBuilder) and a path that is relevant to you,
     * e.g. "dbal.connections": this will strip every node that is not contained in the given path (e.g. the "orm" node
     * would be removed entirely.
     *
     * @param ArrayNode $node
     * @param string $breadcrumbPath
     */
    public static function excludeEverythingNotInBreadcrumbPath(ArrayNode $node, $breadcrumbPath)
    {
        if ($breadcrumbPath === null) {
            return;
        }

        $path = explode('.', $breadcrumbPath);

        self::excludeEverythingNotInPath($node, $path);
    }

    /**
     * @param array $path
     */
    public static function excludeEverythingNotInPath(ArrayNode $node, array $path = array())
    {
        if (empty($path)) {
            return;
        }

        $nextNodeName = array_shift($path);
        $nextNode = self::childNode($node, $nextNodeName);

        $children = self::nodeChildrenProperty()->getValue($node);
        foreach ($children as $name => $child) {
            if ($name !== $nextNodeName) {
                unset($children[$name]);
            }
        }
        self::nodeChildrenProperty()->setValue($node, $children);

        if (!($nextNode instanceof ArrayNode)) {
            if (!empty($path)) {
                throw new ChildIsNotAnArrayNode($node, $nextNodeName);
            }

            return;
        }

        self::excludeEverythingNotInPath($nextNode, $path);
    }

    private static function childNode(ArrayNode $node, $childNodeName)
    {
        $children = self::nodeChildrenProperty()->getValue($node);

        if (!isset($children[$childNodeName])) {
            throw new UndefinedChildNode(
                $node,
                $childNodeName
            );
        }

        return $children[$childNodeName];
    }

    private static function nodeChildrenProperty()
    {
        if (!isset(self::$nodeChildrenProperty)) {
            self::$nodeChildrenProperty = new \ReflectionProperty(
                'Symfony\Component\Config\Definition\ArrayNode',
                'children'
            );
            self::$nodeChildrenProperty->setAccessible(true);
        }

        return self::$nodeChildrenProperty;
    }
}
