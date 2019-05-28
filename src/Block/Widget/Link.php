<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Block\Widget;

use Divante\VsbridgeIndexerCatalog\Api\Data\CatalogConfigurationInterface;
use Divante\VsbridgeIndexerCatalog\Api\SlugGeneratorInterface;
use Divante\VsbridgeIndexerCatalog\Model\ResourceModel\EavAttributesInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class Link
 */
class Link extends \Magento\Catalog\Block\Widget\Link
{

    /**
     * @var string
     */
    private $productUrlSuffix;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CatalogConfigurationInterface
     */
    private $settings;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var EavAttributesInterface
     */
    private $attributeProvider = null;

    /**
     * Link constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param UrlFinderInterface $urlFinder
     * @param CatalogConfigurationInterface $configSettings
     * @param SlugGeneratorInterface $slugGenerator
     * @param \Magento\Catalog\Model\ResourceModel\AbstractResource|null $entityResource
     * @param EavAttributesInterface|null $attributeProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        UrlFinderInterface $urlFinder,
        CatalogConfigurationInterface $configSettings,
        SlugGeneratorInterface $slugGenerator,
        \Magento\Catalog\Model\ResourceModel\AbstractResource $entityResource = null,
        EavAttributesInterface $attributeProvider = null,
        array $data = []
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->settings = $configSettings;
        $this->slugGenerator = $slugGenerator;
        $this->scopeConfig = $context->getScopeConfig();

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

        $urlSuffix = $this->getProductUrlSuffix();
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
        $attributes = $this->loadAttributes($entityId, $storeId);

        if (isset($attributes['name'])) {
            $slug = $this->slugGenerator->generate($attributes['name'], $entityId);
            $attributes['slug'] = $slug;
            $attributes['url_key'] = $slug;
            unset($attributes['name']);
        } else {
            $attributes['slug'] = $attributes['url_key'];
        }

        if ($this->_entityResource instanceof \Magento\Catalog\Model\ResourceModel\Product) {
            $staticAttributes = $this->_entityResource->getAttributeRawValue($entityId, ['sku'], $storeId);
            $attributes['sku'] = $staticAttributes['sku'];
        }

        return $attributes;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     *
     * @return array
     * @throws \Exception
     */
    private function loadAttributes(int $entityId, int $storeId)
    {
        $attributeCodes = [
            'sku',
            'url_key',
        ];

        if ($this->settings->useMagentoUrlKeys()) {
            $attributeCodes[] = 'name';
        }

        $attributes = $this->attributeProvider->loadAttributesData($storeId, [$entityId], $attributeCodes);
        $attributes = $attributes[$entityId];

        return $attributes;
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @return string
     */
    private function getProductUrlSuffix()
    {
        if (null === $this->productUrlSuffix) {
            $this->productUrlSuffix = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX
            );
        }

        return $this->productUrlSuffix;
    }
}
