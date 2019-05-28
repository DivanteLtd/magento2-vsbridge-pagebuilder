<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin;

use Divante\VsbridgePageBuilder\Model\SettingsRegistryInterface;

/**
 * Class TemplatePlugin
 */
class TemplatePlugin
{

    /**
     * @var SettingsRegistryInterface
     */
    private $settingRegistry;

    /**
     * TemplatePlugin constructor.
     *
     * @param SettingsRegistryInterface $settingRegistry
     */
    public function __construct(SettingsRegistryInterface $settingRegistry)
    {
        $this->settingRegistry = $settingRegistry;
    }

    /**
     * @param \Magento\Framework\Filter\Template $subject
     * @param string $value
     *
     * @return array
     */
    public function beforeFilter(\Magento\Framework\Filter\Template $subject, $value)
    {
        if ($this->settingRegistry->getConvertToJson()) {
            $value = $this->updateContentHtml($value);
        }

        return [$value];
    }

    /**
     * @param string $content
     *
     * @return string
     */
    public function updateContentHtml(string $content)
    {
        $content = str_replace(
            "href=\"{{widget type='Magento\Catalog\Block\Product\Widget\Link",
            "link_value=\"{{widget type='Divante\VsbridgePageBuilder\Block\Product\Widget\Link",
            $content
        );

        $content = str_replace(
            "href=\"{{widget type='Magento\Catalog\Block\Category\Widget\Link",
            "link_value=\"{{widget type='Divante\VsbridgePageBuilder\Block\Category\Widget\Link",
            $content
        );

        $content = str_replace(
            "href=\"{{widget type='Magento\Cms\Block\Widget\Page\Link",
            "link_value=\"{{widget type='Divante\VsbridgePageBuilder\Block\Widget\Page\Link",
            $content
        );

        $content = str_replace(
            "\"Magento\CatalogWidget\Block\Product\ProductsList\" template=\"Magento_CatalogWidget::product/widget/content/grid.phtml",
            "\"Divante\VsbridgePageBuilder\Block\Widget\Product\ProductsList\" template=\"Divante_VsbridgePageBuilder::product/widget/grid.phtml",
            $content
        );

        $content = str_replace(
            "Magento_PageBuilder::widget/link_href.phtml",
            "Divante_VsbridgePageBuilder::widget/link.phtml",
            $content
        );

        $content = str_replace(
            "widget/block.phtml",
            "Divante_VsbridgePageBuilder::widget/block.phtml",
            $content
        );

        $content = str_replace(
            "Magento\Banner\Block\Widget\Banner",
            "Divante\VsbridgePageBuilder\Block\Widget\Banner",
            $content
        );

        $content = str_replace(
            "widget/static_block/default.phtml",
            "Divante_VsbridgePageBuilder::widget/static_block/default.phtml",
            $content
        );

        return $content;
    }
}
