<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Api;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PageRepositoryInterface
 */
interface PageRepositoryInterface
{
    /**
     * @param int $pageId
     *
     * @return PageInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $pageId): PageInterface;
}
