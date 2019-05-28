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
 * Class Image
 */
class Image implements RendererInterface
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
     * Slider constructor.
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
        $linkNode = $node->firstChild;
        $render = null;

        if ($linkNode) {
            $render = $this->childrenRendererPool->getRenderer(
                $this->attributeProcessor->getAttributeValue($linkNode, 'data-element')
            );
        }

        $linkSettings = [];
        $html = '';

        if ($render) {
            $linkSettings = $render->toArray($domDocument, $linkNode);

            foreach ($linkNode->childNodes as $childNode) {
                $html .= $domDocument->saveHTML($childNode);
            }
        } else {
            foreach ($node->childNodes as $childNode) {
                $html .= $domDocument->saveHTML($childNode);
            }
        }

        $linkSettings['value'] = $html;
        $item['image_settings'] = $linkSettings;

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
