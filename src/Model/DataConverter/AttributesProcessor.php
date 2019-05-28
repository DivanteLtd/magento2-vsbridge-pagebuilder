<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter;

/**
 * Class AttributesProcessor
 */
class AttributesProcessor
{
    const BACKGROUND_IMAGES_ATTR = 'data-background-images';

    /**
     * @param \DOMElement $node
     * @param string $attribute
     *
     * @return mixed|string
     */
    public function getAttributeValue(\DOMElement $node, string $attribute)
    {
        $value = '';

        if ($node->hasAttribute($attribute)) {
            $dataContentType = $node->attributes->getNamedItem($attribute);
            $value = $dataContentType->nodeValue;
        }

        if (self::BACKGROUND_IMAGES_ATTR === $attribute) {
            $value = json_decode(stripslashes($value), true);
        }

        return $value;
    }

    /**
     * @param \DOMElement $node
     *
     * @return string
     */
    public function getContentType(\DOMElement $node): string
    {
        $contentType = '';

        if ($node->hasAttribute('data-content-type')) {
            $dataContentType = $node->attributes->getNamedItem('data-content-type');
            $contentType = $dataContentType->nodeValue;
        }

        return $contentType;
    }

    /**
     * @param \DOMElement $node
     *
     * @return array
     */
    public function getAttributes($node): array
    {
        $item = [];

        if ($node->hasAttributes()) {
            /** @var \DOMElement $attribute */
            foreach ($node->attributes as $attribute) {
                $item[$attribute->nodeName] = $attribute->nodeValue;
            }
        }

        return $item;
    }
}
