<?php
/**
 * @package  m23ee
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\Page\Command;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface GetInterface
 */
interface GetInterface
{
    /**
     * @param int $pageId
     *
     * @return PageInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $pageId): PageInterface;
}
