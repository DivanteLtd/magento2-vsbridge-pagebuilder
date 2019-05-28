<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\DataProvider;

use Divante\VsbridgePageBuilder\Model;

/**
 * Class CatalogPlugin
 */
class CatalogPlugin
{
    /**
     * @var Model\SettingsRegistry
     */
    private $settingRegistry;

    /**
     * @var Model\Config
     */
    private $config;

    /**
     * @var Model\ConvertPageBuilderToJson
     */
    private $converter;

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $templateFilterFactory;

    /**
     * @var string
     */
    private $templateFilterModel;

    /**
     * CatalogPlugin constructor.
     *
     * @param Model\Config $scopeConfig
     * @param Model\ConvertPageBuilderToJson $convertPageBuilderToJson
     * @param Model\SettingsRegistry $settingRegistry
     * @param \Magento\Catalog\Model\Template\Filter\Factory $templateFilterFactory
     * @param $templateFilterModel
     */
    public function __construct(
        Model\Config $scopeConfig,
        Model\ConvertPageBuilderToJson $convertPageBuilderToJson,
        Model\SettingsRegistry $settingRegistry,
        \Magento\Catalog\Model\Template\Filter\Factory $templateFilterFactory,
        $templateFilterModel
    ) {
        $this->settingRegistry = $settingRegistry;
        $this->config = $scopeConfig;
        $this->converter = $convertPageBuilderToJson;
        $this->templateFilterFactory = $templateFilterFactory;
        $this->templateFilterModel = $templateFilterModel;
    }

    /**
     * @param array $result
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateDescription(array $result): array
    {
        if ($this->config->isPageBuilderEnabled()) {
            $this->settingRegistry->setConvertToJson(true);
            $templateFilterProcessor = $this->getPageTemplateProcessor();

            foreach ($result as $entityId => $attributes) {
                if (isset($attributes['description'])) {
                    $desc = $attributes['description'];
                    $desc = $templateFilterProcessor->filter($desc);
                    $descAsArray = $this->converter->convert($desc);
                    $result[$entityId]['description'] = $descAsArray;
                }
            }
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\Filter\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getPageTemplateProcessor()
    {
        return $this->templateFilterFactory->create($this->templateFilterModel);
    }
}
