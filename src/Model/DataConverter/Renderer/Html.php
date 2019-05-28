<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter\Renderer;

use Divante\VsbridgePageBuilder\Model\DataConverter\AttributesProcessor;
use Divante\VsbridgePageBuilder\Model\DataConverter\RendererInterface;

/**
 * Class Html
 */
class Html implements RendererInterface
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
        $item = $this->attributeProcessor->getAttributes($node);
        $dataContentType = null;
        $html = '';

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $html .= $domDocument->saveHtml($child);
            }
        }

        $item['value'] = $html;

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
