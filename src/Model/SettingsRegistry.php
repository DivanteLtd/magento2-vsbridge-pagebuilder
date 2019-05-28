<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

/**
 * Class SettingsRegistry
 */
class SettingsRegistry extends \Magento\Framework\DataObject implements SettingsRegistryInterface
{

    /**
     * @inheritdoc
     */
    public function getConvertToJson()
    {
        return (bool)$this->getData(self::CONVERT_TO_JSON);
    }

    /**
     * @inheritdoc
     */
    public function setConvertToJson($convert)
    {
        return $this->setData(self::CONVERT_TO_JSON, $convert);
    }
}
