<?php
/**
 * @package  m23ee
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Plugin\DataProvider;

use Divante\VsbridgeIndexerCatalog\Model\Indexer\DataProvider\Category\AttributeData;

/**
 * Class UpdateCategoryDescription
 */
class UpdateCategoryDescription extends CatalogPlugin
{
    /**
     * @param AttributeData $subject
     * @param array $result
     *
     * @return array
     */
    public function afterAddData(AttributeData $subject, array $result)
    {
        return $this->updateDescription($result);
    }
}
