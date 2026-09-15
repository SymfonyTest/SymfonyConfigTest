<?php

namespace Matthias\SymfonyConfigTest\Partial;

use Matthias\SymfonyConfigTest\Partial\Exception\ChildIsNotAnArrayNode;
use Matthias\SymfonyConfigTest\Partial\Exception\UndefinedChildNode;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\PrototypedArrayNode;

class PartialNode
{
    private static ?\ReflectionProperty $nodeChildrenProperty = null;

    /**
     * Provide an ArrayNode instance (e.g. the root node created by a TreeBuilder) and a path that is relevant to you,
     * e.g. "dbal.connections": this will strip every node that is not contained in the given path (e.g. the "orm" node
     * would be removed entirely.
     *
     * @param string|null $breadcrumbPath
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
     * @param list<string> $path
     *
     * @throws ChildIsNotAnArrayNode if the child node is not an array node
     * @throws UndefinedChildNode if the node does not have a child in the given path
     */
    public static function excludeEverythingNotInPath(ArrayNode $node, array $path = [])
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

    /**
     * @param non-empty-string $childNodeName
     *
     * @throws UndefinedChildNode if the node does not have a child with the given name
     */
    private static function childNode(ArrayNode $node, string $childNodeName): NodeInterface
    {
        if ($node instanceof PrototypedArrayNode && '*' === $childNodeName) {
            return $node->getPrototype();
        }

        $children = self::nodeChildrenProperty()->getValue($node);

        if (!isset($children[$childNodeName])) {
            throw new UndefinedChildNode(
                $node,
                $childNodeName
            );
        }

        return $children[$childNodeName];
    }

    private static function nodeChildrenProperty(): \ReflectionProperty
    {
        return self::$nodeChildrenProperty ??= new \ReflectionProperty(
            ArrayNode::class,
            'children'
        );
    }
}
