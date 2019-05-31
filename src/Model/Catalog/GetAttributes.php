<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model\Catalog;

use Divante\VsbridgeIndexerCatalog\Api\Data\CatalogConfigurationInterface;
use Divante\VsbridgeIndexerCatalog\Api\SlugGeneratorInterface;
use Divante\VsbridgeIndexerCatalog\Model\ResourceModel\EavAttributesInterface;

/**
 * Class GetAttributes
 */
class GetAttributes
{

    /**
     * @var CatalogConfigurationInterface
     */
    private $catalogConfiguration;

    /**
     * @var SlugGeneratorInterface
     */
    private $slugGenerator;

    /**
     * @var EavAttributesInterface
     */
    private $attributeProvider = null;

    public function __construct(
        CatalogConfigurationInterface $catalogConfiguration,
        SlugGeneratorInterface $slugGenerator,
        EavAttributesInterface $attributeProvider
    ) {
        $this->catalogConfiguration = $catalogConfiguration;
        $this->slugGenerator = $slugGenerator;
        $this->attributeProvider = $attributeProvider;
    }

    /**
     * @param int $entityId
     * @param int $storeId
     *
     * @throws \Exception
     */
    public function execute(int $entityId, int $storeId)
    {
        $attributes = $this->loadAttributes($entityId, $storeId);

        if (isset($attributes['name'])) {
            $slug = $this->slugGenerator->generate($attributes['name'], $entityId);
            $attributes['slug'] = $slug;
            $attributes['url_key'] = $slug;
            unset($attributes['name']);
        } else {
            $attributes['slug'] = $attributes['url_key'];
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

        if ($this->catalogConfiguration->useMagentoUrlKeys()) {
            $attributeCodes[] = 'name';
        }

        $attributes = $this->attributeProvider->loadAttributesData($storeId, [$entityId], $attributeCodes);
        $attributes = $attributes[$entityId];

        return $attributes;
    }
}
