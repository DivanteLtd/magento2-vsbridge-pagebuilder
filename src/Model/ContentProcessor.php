<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

use Magento\Framework\Filter\Template as TemplateFilter;

/**
 * Class ContentProcessor
 */
class ContentProcessor extends \Divante\VsbridgeIndexerCms\Model\ContentProcessor
{
    /**
     * @var SettingsRegistryInterface
     */
    private $settingRegistry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConvertPageBuilderToJson
     */
    private $converter;

    /**
     * ContentProcessor constructor.
     *
     * @param Config $scopeConfig
     * @param ConvertPageBuilderToJson $convertPageBuilderToJson
     * @param SettingsRegistryInterface $settingRegistry
     */
    public function __construct(
        Config $scopeConfig,
        ConvertPageBuilderToJson $convertPageBuilderToJson,
        SettingsRegistryInterface $settingRegistry
    ) {
        $this->settingRegistry = $settingRegistry;
        $this->config = $scopeConfig;
        $this->converter = $convertPageBuilderToJson;
    }

    /**
     * @param TemplateFilter $templateFilter
     * @param string $content
     *
     * @return array|string
     * @throws \Exception
     */
    public function parse(TemplateFilter $templateFilter, string $content)
    {
        if ($this->config->isPageBuilderEnabled()) {
            $this->settingRegistry->setConvertToJson(true);
            $content = $this->getContentFiltered($templateFilter, $content);
            $contentAsArray = $this->converter->convert($content);

            return $contentAsArray;
        }

        return $this->getContentFiltered($templateFilter, $content);
    }

    /**
     * @param TemplateFilter $templateFilter
     * @param string $content
     *
     * @return string
     * @throws \Exception
     */
    private function getContentFiltered(TemplateFilter $templateFilter, string $content)
    {
        return $templateFilter->filter($content);
    }
}
