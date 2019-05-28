<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\Mapping;

use Divante\VsbridgeIndexerCms\Index\Mapping\CmsPage;
use Divante\VsbridgePageBuilder\Model\Config;

/**
 * Class CmsPagePlugin
 */
class CmsPagePlugin
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
     * @param CmsPage $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMappingProperties(CmsPage $subject, array $result)
    {
        if ($this->config->isPageBuilderEnabled()) {
            unset($result['properties']['content']);
        }

        return $result;
    }
}
