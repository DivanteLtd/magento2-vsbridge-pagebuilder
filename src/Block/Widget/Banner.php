<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Block\Widget;

/**
 * Class Banner
 */
class Banner extends \Magento\Banner\Block\Widget\Banner
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;

    /**
     * Banner constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Banner\Model\ResourceModel\Banner $resource
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Banner\Model\ResourceModel\Banner $resource,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $resource, $data);

        $this->filterProvider = $filterProvider;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $bannerResource = $this->_bannerResource;
        $bannerId = $this->_getData('unique_id');

        if ($bannerId) {
            $content = $bannerResource->getStoreContent($bannerId, $this->_currentStoreId);
            $content = $this->filterProvider->getBlockFilter()->setStoreId($this->_currentStoreId)->filter($content);

            return $content;
        }

        return '';
    }
}
