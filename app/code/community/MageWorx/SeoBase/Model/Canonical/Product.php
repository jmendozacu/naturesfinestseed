<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Product extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    const LONGEST_BY_URL       = 1;
    const SHORTEST_BY_URL      = 2;
    const ROOT                 = 3;
    const LONGEST_BY_CATEGORY  = 4;
    const SHORTEST_BY_CATEGORY = 5;

    protected $_helperStore;

    public function _getCanonicalUrl($item = null)
    {        
        $this->_helperStore = Mage::helper('mageworx_seobase/store');

        if ($this->isCancelCanonical()) {
            return null;
        }

        $product = (is_object($item) && $item->getId()) ? $item : Mage::registry('current_product');
        if (!$product) {
            return null;
        }

        $storeId = Mage::app()->getStore()->getStoreId();

        $personalCanonicalUrlCode = $this->_getPersonalCanonicalUrlCode($product);
        $personalCanonicalUrlPath = $this->_getPersonalCanonicalUrlPath($product);

        if ($personalCanonicalUrlCode) {
            $urlRewrite   = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($personalCanonicalUrlCode);
            $canonicalUrl = trim($urlRewrite->getRequestPath());

            if (strpos($canonicalUrl, 'http') === 0) {
                return htmlspecialchars($canonicalUrl, ENT_COMPAT, 'UTF-8', false);
            } else {
                $canonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . $canonicalUrl;
            }
        } elseif ($personalCanonicalUrlPath) {
            $urlRewrite   = Mage::getModel('core/url_rewrite')->setStoreId($storeId)->loadByIdPath($personalCanonicalUrlPath);
            $canonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . trim($urlRewrite->getRequestPath());
        }

        if ($this->_helperData->isAssociatedCanonicalEnabled($storeId) &&
            $this->_helperData->isCompoundProductType($product->getTypeID()) === false &&
            empty($canonicalUrl)
        ) {
            $compoundProduct = $this->_getLastCompoundProductByChildProductId($product->getId(), $storeId);

            if (is_object($compoundProduct) && $compoundProduct->getId()) {
                return $this->_getCanonicalUrl($compoundProduct);
            }
        }

        $crossDomainStoreId = $this->_getCrossDomainStoreId($product, $storeId);

        if (empty($canonicalUrl) && $crossDomainStoreId) {
            $canonicalUrl = $this->_getUrlRewriteCanonical($product, $crossDomainStoreId);
        }

        if (empty($canonicalUrl)) {
            $canonicalUrl = $this->_getUrlRewriteCanonical($product, $storeId);
            if (!$canonicalUrl) {
                $canonicalUrl = $product->getProductUrl(false);
            }
            
            if (!$canonicalUrl) {
                $useCategories = Mage::helper('mageworx_seobase')->useCategoriesPathInProductUrl($storeId);
                $product->setDoNotUseCategoryId(!$useCategories);
                $canonicalUrl = $product->getProductUrl(false);
            }
        }

        return $this->renderUrl($canonicalUrl);
    }

    protected function _getPersonalCanonicalUrlCode($product)
    {
        $string = trim($product->getCanonicalUrl());
        
        return (preg_match('/^[0-9]+\_{1}[0-9]+$/', $string) === 1) ? $string : null;
    }

    protected function _getPersonalCanonicalUrlPath($product)
    {
        $string = trim($product->getCanonicalUrl());

        return (preg_match('/^[0-9]+\_{1}[0-9]+$/', $string) !== 1) ? $string : null;
    }

    protected function _getUrlRewriteCanonical($product, $storeId)
    {
        if (!is_object($product)) {
            return null;
        }

        $canonicalUrl  = '';
        $canonicalType = $this->_helperData->getProductCanonicalType();

        if (Mage::helper('mageworx_seobase')->isEnterpriseSince113()) {
            
            $canonicalUrl = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_ee')
                ->getCanonicalUrl($product, $canonicalType, $storeId = null);
        }
        else {
            $collection = Mage::getResourceModel('mageworx_seobase/core_url_rewrite_collection')
                ->filterAllByProductId($product->getId())
                ->addStoreFilter($storeId, false)
                ->filterCanonicalUrl($canonicalType);

            $urlRewrite = $collection->getFirstItem();

            if ($urlRewrite && $urlRewrite->getRequestPath()) {
                $canonicalUrl = $this->_helperStore->getStoreBaseUrl($storeId) . $urlRewrite->getRequestPath();
            }
        }

        return $canonicalUrl;
    }

    protected function _getLastCompoundProductByChildProductId($productId, $storeId)
    {
        $ids          = $this->_getParentProductIds($productId);
        $productTypes = $this->_helperData->getProductTypeForReplaceCanonical($storeId);

        if (count($ids) && count($productTypes)) {
            $visibilityStatuses = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH
            );

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addIdFilter($ids)
                ->addStoreFilter($storeId)
                ->setStoreId($storeId)
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addFieldToFilter('visibility', array('in' => $visibilityStatuses))
                ->addAttributeToFilter('type_id', array('in' => $productTypes))
                ->setOrder('entity_id', 'DESC');

            if ($collection->count()) {
                $product = $collection->getFirstItem();
                return $product;
            }
        }
        return null;
    }

    protected function _getCrossDomainStoreId($product, $storeId)
    {
        $personalCrossDomainStoreId = $product->getCanonicalCrossDomain();

        if ($this->_isValidCrossDomainStoreId($personalCrossDomainStoreId, $storeId)) {
            return $personalCrossDomainStoreId;
        }

        $configCrossDomainStoreId = $this->_helperData->getCrossDomainStoreId($storeId);

        if ($this->_isValidCrossDomainStoreId($configCrossDomainStoreId, $storeId)) {
            return $configCrossDomainStoreId;
        }

        return null;
    }

    /**
     * Check if store ID is valid
     *
     * @param int $storeId
     * @return int|false
     */
    protected function _isValidCrossDomainStoreId($crossDomainStoreId, $storeId)
    {
        if (!$crossDomainStoreId) {
            return false;
        }
        if (!$this->_helperStore->isActiveStore($crossDomainStoreId)) {
            return false;
        }
        
        if ($storeId == $crossDomainStoreId) {
            return false;
        }
        return true;
    }

    protected function _getParentProductIds($id)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn         = $coreResource->getConnection('core_read');
        $select       = $conn->select()
            ->from($coreResource->getTableName('catalog/product_relation'), array('parent_id'))
            ->where('child_id = ?', $id);

        return $conn->fetchCol($select);
    }

}