<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer;

use Divante\VsbridgePageBuilder\Model\DataConverter\AttributesProcessor;
use Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRendererInterface;

/**
 * Class Slide
 */
class Slide implements ChildrenRendererInterface
{
    /**
     * @var AttributesProcessor
     */
    private $attributeProcessor;

    /**
     * Slider constructor.
     *
     * @param AttributesProcessor $attributeProcessor
     */
    public function __construct(AttributesProcessor $attributeProcessor)
    {
        $this->attributeProcessor = $attributeProcessor;
    }

    /**
     * @inheritdoc
     */
    public function toArray(\DOMDocument $domDocument, \DOMElement $node): array
    {
        $xpath = new \DOMXPath($domDocument);
        $wrapper = $this->getWrapperNode($xpath, $node);
        $overlayNode = $this->getOverlayNode($xpath, $node);
        $backgroundImages = $this->attributeProcessor->getAttributeValue(
            $wrapper,
            AttributesProcessor::BACKGROUND_IMAGES_ATTR
        );

        $attributes = $this->attributeProcessor->getAttributes($node);

        $settings = [
            'items' => [],
            'wrapper_style' => $this->attributeProcessor->getAttributeValue($wrapper, 'style'),
            'style' => $this->attributeProcessor->getAttributeValue($overlayNode, 'style'),
            AttributesProcessor::BACKGROUND_IMAGES_ATTR => $backgroundImages,
        ];

        $settings = array_merge($settings, $attributes);

        $contentData = $this->getContentAsArray($domDocument, $node);
        $buttonData = $this->getButtonAsArray($domDocument, $node);

        $settings['items'][] = $contentData;

        if (!empty($buttonData)) {
            $settings['items'][] = $buttonData;
        }

        return $settings;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMElement
     */
    private function getWrapperNode(\DOMXPath $xpath, \DOMElement $node)
    {
        $content = $xpath->query('.//*[@data-element="wrapper"]', $node);

        return $content->item(0);
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMElement
     */
    private function getOverlayNode(\DOMXPath $xpath, \DOMElement $node)
    {
        $content = $xpath->query('.//*[@data-element="overlay"]', $node);

        return $content->item(0);
    }

    /**
     * @param \DOMDocument $domDocument
     * @param \DOMElement $node
     *
     * @return array
     */
    private function getContentAsArray(\DOMDocument $domDocument, \DOMElement $node)
    {
        $xpath = new \DOMXPath($domDocument);
        $content = $this->getSlideContent($xpath, $node);
        $childItem = $this->attributeProcessor->getAttributes($content);
        $childItem['value'] = $domDocument->saveHTML($content);

        return $childItem;
    }

    /**
     * @param \DOMDocument $domDocument
     * @param \DOMElement $node
     *
     * @return array
     */
    private function getButtonAsArray(\DOMDocument $domDocument, \DOMElement $node)
    {
        $childItem = [];
        $xpath = new \DOMXPath($domDocument);
        $button = $this->getSlideButton($xpath, $node);

        if ($button) {
            $childItem = $this->attributeProcessor->getAttributes($button);
            $childItem['value'] = $button->nodeValue;
        }

        return $childItem;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMElement
     */
    private function getSlideButton(\DOMXPath $xpath, \DOMElement $node)
    {
        $content = $xpath->query('.//*[@data-element="button"]', $node);

        return $content->item(0);
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMElement $node
     *
     * @return \DOMElement
     */
    private function getSlideContent(\DOMXPath $xpath, \DOMElement $node)
    {
        $content = $xpath->query('.//*[@data-element="content"]', $node);

        return $content->item(0);
    }
}
