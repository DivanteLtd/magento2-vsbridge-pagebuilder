<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\Mapping;

use Divante\VsbridgeIndexerCms\Index\Mapping\CmsBlock;
use Divante\VsbridgePageBuilder\Model\Config;

/**
 * Class CmsBlockPlugin
 */
class CmsBlockPlugin
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
     * @param CmsBlock $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMappingProperties(CmsBlock $subject, array $result)
    {
        if ($this->config->isPageBuilderEnabled()) {
            unset($result['properties']['content']);
        }

        return $result;
    }
}
