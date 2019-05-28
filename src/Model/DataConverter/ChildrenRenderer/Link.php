<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRenderer;

use Divante\VsbridgePageBuilder\Model\DataConverter\ChildrenRendererInterface;

/**
 * Class Link
 */
class Link implements ChildrenRendererInterface
{

    /**
     * @var array
     */
    private $blackListedAttributes = [
        'data-element',
    ];

    /**
     * @inheritdoc
     */
    public function toArray(\DOMDocument $domDocument, \DOMElement $node): array
    {
        $settings = $this->getAttributes($node);

        return $settings;
    }

    /**
     * @param \DOMElement $node
     *
     * @return array
     */
    private function getAttributes($node) : array
    {
        $nodeData = [];

        if ($node->hasAttributes()) {
            /** @var \DOMElement $attribute */
            foreach ($node->attributes as $attribute) {
                $attributeName = (string)$attribute->nodeName;

                if (in_array($attributeName, $this->blackListedAttributes)) {
                    continue;
                }

                $attributeName = $this->getAttributeName($attributeName);
                $value = $attribute->nodeValue;

                if ('link_value' === $attributeName) {
                    $value = json_decode(stripslashes($value), true);

                    if (is_array($value)) {
                        $nodeData = array_merge($nodeData, $value);
                    }
                } elseif ('href' === $attributeName) {
                    $nodeData['url_path'] = $value;
                } else {
                    $nodeData[$attributeName] = $value;
                }
            }

            if ($node->firstChild && 'span' === $node->firstChild->nodeName) {
                $nodeData['link_text'] = $node->firstChild->nodeValue;
            }
        }

        return ['link_settings' => $nodeData];
    }

    /**
     * @param string $attributeName
     *
     * @return string
     */
    private function getAttributeName(string $attributeName) : string
    {
        if ('data-link-type' === $attributeName) {
            $attributeName = 'link_type';
        }

        return $attributeName;
    }
}
