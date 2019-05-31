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
class SettingsRegistry implements SettingsRegistryInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @inheritdoc
     */
    public function convertToJson()
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

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($key === (array)$key) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Get value from _data array without parse key
     *
     * @param   string $key
     * @return  mixed
     */
    private function getData($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }
}
