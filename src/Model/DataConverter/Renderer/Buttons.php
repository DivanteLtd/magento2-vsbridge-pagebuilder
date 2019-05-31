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
        $buttons = $this->getButtonsNodes($xpath, $node);
        $buttons = [];

        foreach ($buttons as $button) {
            $dataContentType = 'button-item';
            $linkRender = $this->childrenRendererPool->getRenderer(
                $this->attributeProcessor->getAttributeValue($button, 'data-element')
            );

            if ($linkRender) {
                $settings = $linkRender->toArray($domDocument, $button);
                $settings['data-content-type'] = 'button-item';
                $buttons[] = $settings;
            }
        }

        $item['items'] = $buttons;

        return $item;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMNodeList
     */
    private function getButtonsNodes(\DOMXPath $xpath, \DOMElement $node)
    {
        return $xpath->query('.//*[contains(@class, "pagebuilder-button")]', $node);
    }

    /**
     * @inheritdoc
     */
    public function processChildren(): bool
    {
        return false;
    }
}
