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
class Banner implements RendererInterface
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
        $contentType = $this->attributeProcessor->getContentType($node);
        $render = $this->childrenRendererPool->getRenderer($contentType);

        if ($render) {
            $bannerSettings = $render->toArray($domDocument, $node);
            $linkNode = $node->firstChild;
            $linkRender = $this->childrenRendererPool->getRenderer(
                $this->attributeProcessor->getAttributeValue($linkNode, 'data-element')
            );

            if ($linkRender) {
                $settings = $linkRender->toArray($domDocument, $linkNode);
                $bannerSettings['link_settings'] = $settings['link_settings'];
            }

            $item['banner_settings'] = $bannerSettings;
        }

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
