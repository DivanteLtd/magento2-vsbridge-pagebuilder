<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\Page\Command;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\App\AreaList;

use Divante\VsbridgeIndexerCms\Model\ContentProcessor;

/**
 * Class Get
 */
class Get implements GetInterface
{
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * @var ContentProcessor
     */
    private $contentProcessor;

    /**
     * Get constructor.
     *
     * @param AreaList $areaList
     * @param State $appState
     * @param FilterProvider $filterProvider
     * @param ContentProcessor $contentProcessor
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     */
    public function __construct(
        AreaList $areaList,
        State $appState,
        FilterProvider $filterProvider,
        ContentProcessor $contentProcessor,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->areaList = $areaList;
        $this->pageFactory = $pageFactory;
        $this->filterProvider = $filterProvider;
        $this->contentProcessor = $contentProcessor;
        $this->appState = $appState;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $pageId): PageInterface
    {
        $page = $this->pageFactory->create();
        $page->load($pageId);

        if (!$page->getId()) {
            throw new NoSuchEntityException(__('The CMS page with the "%1" ID doesn\'t exist.', $pageId));
        }

        $content = $this->contentProcessor->parse($this->filterProvider->getPageFilter(), $page->getContent());
        $page->setContent($content);

        return $page;
    }

    /**
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    private function getContentFiltered(string $content)
    {
        $this->areaList->getArea(Area::AREA_FRONTEND)->load(Area::PART_DESIGN);

        $emulatedResult = $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$this->filterProvider->getPageFilter(), 'filter'],
            [$content]
        );

        return $emulatedResult;
    }
}
