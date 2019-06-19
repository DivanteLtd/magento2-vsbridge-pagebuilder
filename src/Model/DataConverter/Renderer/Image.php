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
     * @var array
     */
    private $blackListAttributes = [
        'class',
        'data-element',
        'src',
    ];

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
        $linkNode = $node->firstChild;
        $render = null;

        if ($linkNode) {
            $render = $this->childrenRendererPool->getRenderer(
                $this->attributeProcessor->getAttributeValue($linkNode, 'data-element')
            );
        }

        $linkSettings = [];

        if ($render) {
            $linkSettings = $render->toArray($domDocument, $linkNode);
            $imagesOptions = $this->getImagesOptions($xpath, $linkNode);
        } else {
            $imagesOptions = $this->getImagesOptions($xpath, $node);
        }

        $item['image_settings'] = $imagesOptions;
        $item['image_settings']['link_settings'] = [];

        if (isset($linkSettings['link_settings'])) {
            $item['image_settings']['link_settings' ] = $linkSettings['link_settings'];
        }

        return $item;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return array
     */
    private function getImagesOptions(\DOMXPath $xpath, \DOMElement $node)
    {
        $imgNodes = $this->getImagesNodes($xpath, $node);
        $options = [];

        foreach ($imgNodes as $imgNode) {
            $dataElement = $this->attributeProcessor->getAttributeValue($imgNode, 'data-element');
            $src = $this->attributeProcessor->getAttributeValue($imgNode, 'src');

            if (empty($options)) {
                $options = $this->getImgBaseAttributes($imgNode);
            }

            $options['scrset'][$dataElement] = $src;
        }

        return $options;
    }

    /**
     * @param \DOMElement $node
     *
     * @return array
     */
    private function getImgBaseAttributes(\DOMElement $node): array
    {
        $attributes = $this->attributeProcessor->getAttributes($node);

        foreach ($this->blackListAttributes as $attribute) {
            unset($attributes[$attribute]);
        }

        return $attributes;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMNodeList
     */
    private function getImagesNodes(\DOMXPath $xpath, \DOMElement $node)
    {
        return $xpath->query('.//img', $node);
    }

    /**
     * @inheritdoc
     */
    public function processChildren(): bool
    {
        return false;
    }
}
