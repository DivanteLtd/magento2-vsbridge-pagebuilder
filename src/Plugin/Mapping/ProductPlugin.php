<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\Mapping;

use Divante\VsbridgeIndexerCatalog\Index\Mapping\Product;
use Divante\VsbridgePageBuilder\Model\Config;

/**
 * Class ProductPlugin
 */
class ProductPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * CmsBlockPlugin constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Product $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMappingProperties(Product $subject, array $result)
    {
        if ($this->config->isPageBuilderEnabled()) {
            unset($result['properties']['description']);
        }

        return $result;
    }
}
