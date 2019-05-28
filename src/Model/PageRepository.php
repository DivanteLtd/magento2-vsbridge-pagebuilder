<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

use Divante\VsbridgePageBuilder\Api\PageRepositoryInterface;
use Divante\VsbridgePageBuilder\Model\Page\Command\GetInterface;
use Magento\Cms\Api\Data\PageInterface;

/**
 * Class PageRepository
 */
class PageRepository implements PageRepositoryInterface
{
    /**
     * @var GetInterface
     */
    private $commandGet;

    public function __construct(GetInterface $commandGet)
    {
        $this->commandGet = $commandGet;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $pageId): PageInterface
    {
        return $this->commandGet->execute($pageId);
    }
}
