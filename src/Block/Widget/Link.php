<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Block\Widget;

use Divante\VsbridgePageBuilder\Model\Catalog\GetAttributes;
use Magento\CatalogUrlRewrite\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class Link
 */
class Link extends \Magento\Catalog\Block\Widget\Link
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Divante\VsbridgePageBuilder\Model\Catalog\GetAttributes
     */
    private $getAttributes;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param UrlFinderInterface $urlFinder
     * @param \Divante\VsbridgePageBuilder\Model\Catalog\GetAttributes $getAttributes
     * @param \Magento\Catalog\Model\ResourceModel\AbstractResource|null $entityResource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        UrlFinderInterface $urlFinder,
        GetAttributes $getAttributes,
        \Magento\Catalog\Model\ResourceModel\AbstractResource $entityResource = null,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->getAttributes = $getAttributes;

        parent::__construct($context, $urlFinder, $entityResource, $data);
    }

    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getJson()
    {
        if (!$this->getData('id_path')) {
            throw new \RuntimeException('Parameter id_path is not set.');
        }

        $rewriteData = $this->parseIdPath($this->getData('id_path'));
        $entityId = $rewriteData[1];

        $store = $this->hasStoreId() ? $this->_storeManager->getStore($this->getStoreId())
            : $this->_storeManager->getStore();
        $filterData = [
            UrlRewrite::ENTITY_ID => $rewriteData[1],
            UrlRewrite::ENTITY_TYPE => $rewriteData[0],
            UrlRewrite::STORE_ID => $store->getId(),
        ];

        $urlSuffix = $this->getSuffix();
        $rewrite = $this->urlFinder->findOneByData($filterData);
        $data = [];

        if ($rewrite) {
            $requestPath = $rewrite->getRequestPath();
            $requestPath = mb_substr($requestPath, 0, -strlen($urlSuffix));
            $data = $this->getAttributes((int)$entityId, $store);
            $data['url_path'] = $requestPath;
        }

        $json = json_encode($data);
        $json = addslashes($json);
        $json = htmlspecialchars($json, ENT_QUOTES);

        return $json;
    }

    /**
     * @param int $entityId
     * @param \Magento\Store\Api\Data\StoreInterface $store
     *
     * @return array
     * @throws \Exception
     */
    private function getAttributes(int $entityId, \Magento\Store\Api\Data\StoreInterface $store)
    {
        $storeId = (int)$store->getId();
        $attributes = $this->getAttributes->execute($entityId, $store->getId());

        if ($this->_entityResource instanceof \Magento\Catalog\Model\ResourceModel\Product) {
            $staticAttributes = $this->_entityResource->getAttributeRawValue($entityId, ['sku'], $storeId);
            $attributes['sku'] = $staticAttributes['sku'];
        }

        return $attributes;
    }

    /**
     * @return string
     */
    private function getSuffix()
    {
        if ($this->_entityResource instanceof \Magento\Catalog\Model\ResourceModel\Product) {
            return $this->geProductUrlSuffix();
        }

        return $this->getCategoryUrlSuffix();
    }

    /**
     * @return string
     */
    private function geProductUrlSuffix()
    {
        return (string)$this->scopeConfig->getValue(Model\ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX);
    }

    /**
     * @return string
     */
    private function getCategoryUrlSuffix()
    {
        return (string)$this->scopeConfig->getValue(Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX);
    }
}
