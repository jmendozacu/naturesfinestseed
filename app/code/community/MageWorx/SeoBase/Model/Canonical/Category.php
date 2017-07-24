<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Canonical_Category extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    protected $_helperLF;

    protected function _getCanonicalUrl($item = null)
    {
        $this->_helperLF = Mage::helper('mageworx_seobase/layeredFilter');

        $category = $item ? $item : Mage::registry('current_category');

        if (!is_object($category)) {
            return '';
        }

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url        = $currentUrl;

        $toolbar = Mage::app()->getLayout()->getBlock('product_list_toolbar');

        if ($this->_helperLF->applyedLayeredNavigationFilters()) {
            if ($this->_helperData->isIncludeLNFiltersToCanonicalUrlByConfig() ==
                MageWorx_SeoBase_Helper_LayeredFilter::CATEGORY_LN_CANONICAL_OFF
            ) {
                return '';
            }
            $url = $this->_getCanonicalUrlForCategoryLNPage($category, $currentUrl, $toolbar);
        }
        else {
            $url = $this->_getCanonicalUrlForCategoryPage($category, $currentUrl, $toolbar);
        }

        return $url ? $this->trailingSlash($url) : $category->getUrl();
    }

    protected function _getCanonicalUrlForCategoryPage($category, $url, $toolbar)
    {
        ///Magento bug? For category with display mode = PAGE,
        /// If clear LN filters the pager will remain in the category URL
        if ($category->getDisplayMode() == 'PAGE') {
            return $this->_helperData->trailingSlash($category->getUrl());
        }

        $availableLimit = $this->_getAvailableLimit($toolbar);

        ///CATEGORY URLS WITH PAGE ALL
        if (is_array($availableLimit) && !empty($availableLimit['all'])) {
            $url = $this->_helperData->trailingSlash($category->getUrl());
            $url = $this->_addLimitAllToUrl($url, $toolbar);
        }

        ///CATEGORY URLS WITHOUT PAGE ALL
        else {
            $url = $this->_changePagerParameterToCurrentForCurrentUrl();
            $url = $this->_cropDefaultLimit($url, $toolbar);
            $url = $this->_deleteSortParameters($url, $toolbar);
        }

        return !empty($url) ? $url : '';
    }

    protected function _getCanonicalUrlForCategoryLNPage($category, $url, $toolbar)
    {
        $availableLimit = $this->_getAvailableLimit($toolbar);

        ///FRIENDLY LN URLS
        if ($this->_helperLF->isLNFriendlyUrlsEnabled()) {
            ///FRIENDLY LN URLS WITH PAGE ALL

            if (is_array($availableLimit) && !empty($availableLimit['all'])) {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($url, $toolbar);
                    $url = $this->_deleteLimitParameter($url, $toolbar);
                    $url = $this->_deletePagerParameter($url, $toolbar);
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
                else {
                    $url = $this->_helperData->trailingSlash($category->getUrl());
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
            }
            ///FRIENDLY LN URLS WITHOUT PAGE ALL
            else {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_changePagerParameterToCurrentForCurrentUrl();
                    $url = $this->_cropDefaultLimit($url, $toolbar);
                    $url = $this->_deleteSortParameters($url, $toolbar);
                }
                else {
                    //Maybe better without canonical url...?
                    $url = $this->_helperData->trailingSlash($category->getUrl());
                }
            }
        }
        ///DEFAULT LN URLS
        else {

            $subCategory = $this->_getSubCategoryForCanonical($url);

            if (is_object($subCategory)) {
                $url = $this->_convertSubCategoryUrl($url, $subCategory);
                if ($subCategory->getDisplayMode() == 'PAGE') {
                    return $this->_helperData->trailingSlash($url);
                }
            }

            ///DEFAULT LN URLS WITH PAGE ALL
            if (is_array($availableLimit) && !empty($availableLimit['all'])) {

                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($url, $toolbar);
                    $url = $this->_deleteLimitParameter($url, $toolbar);
                    $url = $this->_deletePagerParameter($url, $toolbar);
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
                else {
                    $url = $this->_helperData->trailingSlash($category->getUrl());
                    $url = $this->_addLimitAllToUrl($url, $toolbar);
                }
            }
            ///DEFAULT LN URLS WITHOUT PAGE ALL
            else {
                if ($this->_helperLF->isIncludeLNFiltersToCanonicalUrl()) {
                    $url = $this->_deleteSortParameters($url, $toolbar);
                }
                else {
                    //Maybe without canonical url better...?
                    $url = $this->_helperData->trailingSlash($category->getUrl());
                }
            }
        }

        return !empty($url) ? $url : '';
    }

    protected function _getAvailableLimit($toolbar)
    {
        if (is_object($toolbar) && ($toolbar instanceof Mage_Catalog_Block_Product_List_Toolbar)) {
            $availableLimit = $toolbar->getAvailableLimit();

            if (!Mage::helper('mageworx_seobase')->isUseLimitAll() && !empty($availableLimit['all'])) {
                unset($availableLimit['all']);
            }
        }
        else {
            $availableLimit = false;
        }

        return $availableLimit;
    }

    protected function _getSubCategoryForCanonical($url)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        parse_str(html_entity_decode($parseUrl['query']), $params);
        if (!empty($params['cat']) && is_numeric($params['cat'])) {
            $catId       = $params['cat'];
            $subCategory = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($catId);
        }
        return (!empty($subCategory)) ? $subCategory : false;
    }

    protected function _cropDefaultLimit($url, $toolbar)
    {
        if (is_callable(array($toolbar, 'getDefaultPerPageValue')) && is_callable(array($toolbar, 'getLimit'))) {
            if ($toolbar->getDefaultPerPageValue() == $toolbar->getLimit()) {
                $url = $this->_deleteLimitParameter($url, $toolbar);
            }
        }
        return $url;
    }

    /**
     * Retrive current URL with a specified pager: with parameter 'p =' or as URL part: '* [page_number] * '.html? =...
     * Example 1:
     *      Old url from google search: example.com/computers?p=2
     *      Retrive url: example.com/computers-page2.html (If friendly pager ON, etc.)
     * Example 2 (with layered, sort and mode params):
     *      Old url from google search: example.com/electronics/lnav/price:0-1000.html?p=3&limit=15&mode=list
     *      Retrive url:                example.com/electronics/lnav/price:0-1000-page3.html?limit=15&mode=list
     * @return string
     */
    protected function _changePagerParameterToCurrentForCurrentUrl()
    {
        $pageNum = $this->getPageNumFromUrl();
        $pager   = Mage::app()->getLayout()->getBlock('product_list_toolbar_pager');

        //If friendly url disable
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html?p=1,
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //If friendly url enable and use custom pager
        //Canonical for ex.com/computers.html     is ex.com/computers.html
        //Canonical for ex.com/computers.html?p=1 is ex.com/computers.html
        //Because for custom pager url we don't use '1'

        if (is_object($pager)) {
            if (!$pageNum) {
                return Mage::helper('core/url')->getCurrentUrl();
            }
            elseif ($pageNum == 1 && $this->_helperLF->isLNFriendlyUrlsEnabled() && $this->_helperData->getPagerUrlFormat()) {
                return $this->_deletePagerParameter(Mage::helper('core/url')->getCurrentUrl(),
                        $this->getLayout()->getBlock('product_list_toolbar'));
            }
            else {
                return $pager->getPageUrl($pageNum);
            }
        }

        return Mage::helper('core/url')->getCurrentUrl();
    }

    /**
     * Parse url and retrive page number.
     * At first find by param '[?&]p=', then by part of url (E.g. ex.com/apparel-page2.html).
     * @return type
     */
    public function _getPageNumFromUrl()
    {
        $params = Mage::app()->getFrontController()->getRequest()->getParams();
        if (!empty($params['p'])) {
            if (settype($params['p'], 'int') == $params['p']) {
                $num = $params['p'];
            }
        }

        if (empty($num)) {
            $pageFormat = $this->_helperData->getPagerUrlFormat();
            if ($pageFormat != '-[page_number]') {
                $pattern = '(' . str_replace('[page_number]', '[0-9]+', $pageFormat) . ')';
                if (preg_match($pattern,
                        Mage::app()->getFrontController()->getAction()->getRequest()->getRequestString(), $matches)) {
                    $match = array_pop($matches);
                    if ($match) {
                        $lengthBeforeNum = strpos($pageFormat, '[page_number]');
                        $lengthAfterNum  = strlen($pageFormat) - (strpos($pageFormat, '[page_number]') + 13);
                        $num             = substr($match, $lengthBeforeNum);
                        $num             = substr($num, 0, strlen($num) - $lengthAfterNum);
                    }
                }
            }
        }
        return !empty($num) ? $num : false;
    }

    public function _deletePagerParameter($url, $toolbar)
    {
        $pagerFormat = $this->_helperData->getPagerUrlFormat();
        if ($pagerFormat) {
            $pattern         = '#' . str_replace('[page_number]', '[0-9]+', $pagerFormat) . '#';
            $urlWithoutPager = preg_replace($pattern, '', $url);
            $url             = (is_null($urlWithoutPager)) ? $url : $urlWithoutPager;
            return $url;
        }

        return parent::_deletePagerParameter($url, $toolbar);
    }

    protected function _convertSubCategoryUrl($url, $category)
    {

        $parseUrl    = parse_url($url);
        $categoryUrl = $category->getUrl();

        if (!empty($categoryUrl)) {
            $url = $categoryUrl . '?' . $parseUrl['query'];
            $url = $this->deleteParametrsFromUrl($url, array('cat'));
        }

        return $url;
    }

    public function deleteParametrsFromUrl($url, array $cropParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        parse_str(html_entity_decode($parseUrl['query']), $params);

        foreach ($cropParams as $cropName) {
            if (array_key_exists($cropName, $params)) {
                unset($params[$cropName]);
            }
        }

        $queryString = '';
        foreach ($params as $name => $value) {
            if ($queryString == '') {
                $queryString = '?' . $name . '=' . $value;
            }
            else {
                $queryString .= '&' . $name . '=' . $value;
            }
        }

        $url = $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'] . $queryString;
        return $url;
    }
}