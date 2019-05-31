<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Block\Widget\Product;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;

/**
 * Class ProductsList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * ProductsList constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     * @param Json|null $json
     * @param LayoutFactory|null $layoutFactory
     * @param EncoderInterface|null $urlEncoder
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );

        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        return [
            'VSBRIDGE_CATALOG_PRODUCTS_LIST_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            (int) $this->getRequest()->getParam($this->getData('page_var_name'), 1),
            $this->getProductsPerPage(),
            $this->getProductsCount(),
            $conditions,
            $this->json->serialize($this->getRequest()->getParams()),
            $this->getTemplate(),
            $this->getTitle()
        ];
    }

    /**
     * Prepare and return product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @SuppressWarnings(PHPMD.RequestAwareBlockMethod)
     */
    public function createCollection()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();

        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }

        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));

        $conditions = $this->getConditions();
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);

        return $collection;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->getProductCollection();
        $skuList = [];

        foreach ($collection as $item) {
            $skuList[] = $item->getSku();
        }

        return $this->prepareJson($skuList);
    }

    /**
     * @param array $skuList
     *
     * @return string
     */
    private function prepareJson(array $skuList)
    {
        $json = json_encode($skuList);
        $json = addslashes($json);
        $json = htmlspecialchars($json, ENT_QUOTES);

        return $json;
    }
}
