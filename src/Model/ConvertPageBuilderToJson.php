<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

use Divante\VsbridgePageBuilder\Model\DataConverter\RendererPool;

/**
 * Class ConvertPageBuilderToJson
 */
class ConvertPageBuilderToJson
{
    const ROW_PATTERN = '/*[@data-content-type="row"]/*[@data-element="inner"]';
    const TAB_LIST_COMPONENT = 'tablist';
    const TAB_COMPONENT = 'tab';

    const BLOCK_TYPES = [
        'block',
        'dynamic_block'
    ];

    /**
     * @var RendererPool
     */
    private $rendererPool;

    /**
     * ConvertPageBuilderToJson constructor.
     *
     * @param RendererPool $rendererPool
     */
    public function __construct(RendererPool $rendererPool)
    {
        $this->rendererPool = $rendererPool;
    }

    /**
     * @param string $html
     *
     * @return array
     */
    public function convert(string $html)
    {
        $domDocument = $this->createDomDocument($html);
        $result = [];

        $xpath = new \DOMXPath($domDocument);
        $nodes = $xpath->query('//body'. self::ROW_PATTERN);
        /* @var \DOMElement $node */
        foreach ($nodes as $node) {
            /** @var \DOMNamedNodeMap $attributes */
            $attributes = $node->attributes;
            $childNodes = $node->childNodes;

            $item = ['data-content-type' => 'row'];
            /** @var \DOMElement $attribute */
            foreach ($attributes as $attribute) {
                $item[$attribute->nodeName] = $attribute->nodeValue;
            }

            $childOptions = $this->convertItems($domDocument, $childNodes);
            $item['items'] = $childOptions['children'];

            // @TODO fix me, add option to configuration
            $item['full-width'] = $childOptions['has_slider'];

            $result[] = $item;
        }

        if (!$nodes->length && !empty($html)) {
            return $this->getDefaultRow($html);
        }

        return $result;
    }

    /**
     * @param string $html
     *
     * @return array
     */
    private function getDefaultRow(string $html)
    {
        return [
            'data-content-type' => 'row',
            'data-element' => 'inner',
            'items' => [
                [
                    'data-content-type' => 'html',
                    "style" => "border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;",
                    'value' => $html,
                ]
            ]
        ];
    }

    /**
     * @param \DOMDocument $domDocument
     * @param \DOMNodeList $nodes
     *
     * @return array
     */
    private function convertItems(\DOMDocument $domDocument, \DOMNodeList $nodes)
    {
        $xpath = new \DOMXPath($domDocument);
        $items = [];
        $hasSlider = false;

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {
            if ($node->attributes) {
                $childNodes = $this->getChildNodes($xpath, $node);
                $contentType = $this->getContentType($node);

                if (self::TAB_LIST_COMPONENT !== $contentType) {
                    $renderer = $this->rendererPool->getRenderer($contentType);
                    $childrenItemsArray = [];

                    if (!empty($childNodes) && $renderer && $renderer->processChildren()) {
                        $childrenOptions = $this->convertItems($domDocument, $childNodes);
                        $childrenItemsArray = $childrenOptions['children'];
                    }

                    if ($renderer) {
                        $item = $renderer->toArray($domDocument, $node);

                        if (!empty($childrenItemsArray)) {
                            $item['items'] = $childrenItemsArray;
                        }

                        $items[] = $item;

                        if ($renderer instanceof \Divante\VsbridgePageBuilder\Model\DataConverter\Renderer\Slider) {
                            $hasSlider = true;
                        }
                    }
                }
            }
        }

        $itemsOptions = [
            'children' => $items,
            'has_slider' => $hasSlider,
        ];

        return $itemsOptions;
    }

    /**
     * @param \DOMElement $node
     *
     * @return string
     */
    private function getContentType(\DOMElement $node)
    {
        $dataContentType = $node->attributes->getNamedItem('data-content-type');

        if ($dataContentType instanceof \DOMNode) {
            $contentType = $dataContentType->nodeValue;
        } else {
            $contentType = 'default';
            $dataRole = $node->attributes->getNamedItem('role');

            if ($dataRole instanceof \DOMNode) {
                $contentType = (string)$dataRole->nodeValue;
            }
        }

        return $contentType;
    }

    /**
     * @param \DOMXPath $xpath
     * @param \DOMNode $node
     *
     * @return \DOMNodeList
     */
    private function getChildNodes(\DOMXPath $xpath, \DOMNode $node)
    {
        $contentType = 'default';
        $dataContentType = $node->attributes->getNamedItem('data-content-type');

        if ($dataContentType instanceof \DOMNode) {
            $contentType = $dataContentType->nodeValue;
        }

        if (self::TAB_COMPONENT === $contentType) {
            return $xpath->query('//*[@data-content-type="tab-item"]', $node);
        }

        if (in_array($contentType, self::BLOCK_TYPES)) {
            $children = $xpath->query('.' . self::ROW_PATTERN, $node);

            if ($children->length) {
                $children->item(0)->setAttribute('data-content-type', 'row');
            }

            return $children;
        }

        return $node->childNodes;
    }

    /**
     * @param string $html
     *
     * @return \DOMDocument
     */
    private function createDomDocument(string $html) : \DOMDocument
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        set_error_handler(
            function ($errorNumber, $errorString) {
                throw new \Exception($errorString, $errorNumber);
            }
        );

        $string = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        try {
            libxml_use_internal_errors(true);
            $domDocument->loadHTML(
                '<html><body>' . $string . '</body></html>'
            );
            libxml_clear_errors();
        } catch (\Exception $e) {
            restore_error_handler();
        }

        restore_error_handler();

        return $domDocument;
    }
}
