<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\Mapping;

use Divante\VsbridgeIndexerCatalog\Index\Mapping\Category;
use Divante\VsbridgePageBuilder\Model\Config;

/**
 * Class CategoryPlugin
 */
class CategoryPlugin
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
     * @param Category $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMappingProperties(Category $subject, array $result)
    {
        if ($this->config->isPageBuilderEnabled()) {
            unset($result['properties']['description']);
        }

        return $result;
    }
}
