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
 * Class Line
 */
class Line implements ChildrenRendererInterface
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
        $settings['style'] = $this->attributeProcessor->getAttributeValue($node, 'style');

        return $settings;
    }
}
