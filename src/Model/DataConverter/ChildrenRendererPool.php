<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\DataConverter;

/**
 * Class ChildrenRendererPool
 */
class ChildrenRendererPool
{
    /**
     * @var array
     */
    private $renderers;

    /**
     * Constructor
     *
     * @param array $renderers
     */
    public function __construct(
        array $renderers
    ) {
        $this->renderers = $renderers;
    }

    /**
     * Get renderer for content type
     *
     * @param string $contentType
     * @return ChildrenRendererInterface
     */
    public function getRenderer(string $contentType) : ?ChildrenRendererInterface
    {
        if (isset($this->renderers[$contentType])) {
            return $this->renderers[$contentType];
        }

        return null;
    }
}
