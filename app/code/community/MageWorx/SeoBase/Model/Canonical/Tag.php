<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Tag extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    protected function _getCanonicalUrl($item = null)
    {
        $toolbar = mage::app()->getLayout()->getBlock('product_list_toolbar');

        if (is_object($toolbar) && ($toolbar instanceof Mage_Catalog_Block_Product_List_Toolbar)) {
            $availableLimit = $toolbar->getAvailableLimit();
        }
        else {
            $availableLimit = false;
        }

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url        = $currentUrl;

        if (is_array($availableLimit) && !empty($availableLimit['all'])) {
            $url = $this->_deleteSortParameters($url, $toolbar);
            $url = $this->_deleteLimitParameter($url, $toolbar);
            $url = $this->_deletePagerParameter($url, $toolbar);
            $url = $this->_addLimitAllToUrl($url, $toolbar);
        }
        else {
            $url = $this->_deleteSortParameters($url, $toolbar);
        }

        if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            $canonicalUrl = $url;
        }
        else {
            $canonicalUrl = $currentUrl;
        }
        return $canonicalUrl;
    }

}