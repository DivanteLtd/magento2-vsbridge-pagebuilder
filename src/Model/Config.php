<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config
{
    const IS_PAGEBUILDER_ENABLED = 'cms/pagebuilder/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ContentProcessor constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isPageBuilderEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::IS_PAGEBUILDER_ENABLED);
    }
}
