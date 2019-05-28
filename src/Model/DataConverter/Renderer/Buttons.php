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
 * Class Banner
 */
class Buttons implements RendererInterface
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
        $xpath = new \DOMXPath($domDocument);
        $item = $this->attributeProcessor->getAttributes($node);

        $button = $this->getButtonNode($xpath, $node);
        $linkRender = $this->childrenRendererPool->getRenderer(
            $this->attributeProcessor->getAttributeValue($button, 'data-element')
        );

        if ($linkRender) {
            $settings = $linkRender->toArray($domDocument, $button);
            $item['link_settings'] = $settings['link_settings'];
        }

        return $item;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMElement
     */
    private function getButtonNode(\DOMXPath $xpath, \DOMElement $node)
    {
        $content = $xpath->query('.//*[contains(@class, "pagebuilder-button")]', $node);

        return $content->item(0);
    }

    /**
     * @inheritdoc
     */
    public function processChildren(): bool
    {
        return false;
    }
}
