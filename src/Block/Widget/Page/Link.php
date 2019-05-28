<?php
/**
 * @package  Divante\VsbridgePageBuilder
 * @author Agata Firlejczyk <afirlejczyk@divante.pl>
 * @copyright 2019 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\VsbridgePageBuilder\Block\Widget\Page;

/**
 * Class Link
 */
class Link extends \Magento\Cms\Block\Widget\Page\Link
{
    /**
     * @return string
     */
    public function getJson()
    {
        $pageId = $this->getData('page_id');
        $identifier = $this->_resourcePage->getCmsPageIdentifierById($pageId);
        $data = ['url_path' => $identifier];

        $json = json_encode($data);
        $json = addslashes($json);
        $json = htmlspecialchars($json, ENT_QUOTES);

        return $json;
    }
}
