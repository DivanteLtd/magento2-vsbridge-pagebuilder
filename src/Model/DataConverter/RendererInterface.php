<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter;

/**
 * Interface RendererInterface
 */
interface RendererInterface
{
    const BACKGROUND_IMAGES_ATTR = 'data-background-images';

    /**
     * Convert HTML into Array
     *
     * @param \DOMDocument $domDocument
     * @param \DOMElement $node
     * @return array
     * @throws \InvalidArgumentException
     */
    public function toArray(\DOMDocument $domDocument, \DOMElement $node) : array;

    /**
     * @return bool
     */
    public function processChildren() : bool;
}
