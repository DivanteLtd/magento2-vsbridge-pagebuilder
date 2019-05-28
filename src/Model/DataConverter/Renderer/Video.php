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
 * Class Video
 */
class Video implements RendererInterface
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
        $inner = $node->firstChild;
        $wrapper = $inner->firstChild;

        if ($wrapper->attributes && $wrapper->attributes->getNamedItem('style')) {
            $item['style'] = $wrapper->attributes->getNamedItem('style')->nodeValue;
        }

        $container = $wrapper->firstChild;
        $video = $container->firstChild;

        $item['src'] = $video->attributes->getNamedItem('src')->nodeValue;

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
