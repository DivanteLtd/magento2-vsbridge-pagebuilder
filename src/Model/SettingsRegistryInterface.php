<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Model;

/**
 * Interface SettingsRegistryInterface
 */
interface SettingsRegistryInterface
{
    const CONVERT_TO_JSON = 'convert_to_json';

    /**
     * @param bool $convert
     *
     * @return $this
     */
    public function setConvertToJson($convert);

    /**
     * @return bool
     */
    public function getConvertToJson();
}
