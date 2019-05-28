<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter;

/**
 * Interface ChildrenRendererInterface
 */
interface ChildrenRendererInterface
{
    /**
     * @param \DOMDocument $domDocument
     * @param \DOMElement $node
     *
     * @return array
     */
    public function toArray(\DOMDocument $domDocument, \DOMElement $node) : array;
}
