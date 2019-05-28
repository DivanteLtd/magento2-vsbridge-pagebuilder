<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter\Renderer;

use Divante\VsbridgePageBuilder\Model\DataConverter\AttributesProcessor;
use Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRendererPool;
use Divante\VsbridgePageBuilder\Model\DataConverter\RendererInterface;

/**
 * Class Divider
 */
class Divider implements RendererInterface
{

    /**
     * @var ChildrenRendererPool
     */
    private $childrenRendererPool;

    /**
     * @var AttributesProcessor
     */
    private $attributeProcessor;

    /**
     * Divider constructor.
     *
     * @param AttributesProcessor $attributeProcessor
     * @param ChildrenRendererPool $childrenRendererPool
     */
    public function __construct(
        AttributesProcessor $attributeProcessor,
        ChildrenRendererPool $childrenRendererPool
    ) {
        $this->attributeProcessor = $attributeProcessor;
        $this->childrenRendererPool = $childrenRendererPool;
    }

    /**
     * @inheritdoc
     */
    public function toArray(\DOMDocument $domDocument, \DOMElement $node): array
    {
        $item = $this->attributeProcessor->getAttributes($node);
        $hrNode = $node->firstChild;
        $render = null;

        if ($hrNode) {
            $render = $this->childrenRendererPool->getRenderer(
                $this->attributeProcessor->getAttributeValue($hrNode, 'data-element')
            );
        }

        $hrSettings = [];

        if ($render) {
            $hrSettings = $render->toArray($domDocument, $hrNode);
        }

        $item['line_settings'] = $hrSettings;

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function processChildren(): bool
    {
        return false;
    }
}
