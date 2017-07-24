<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


abstract class MageWorx_SeoBase_Model_Canonical_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Value for crop slash
     */
    const TRAILING_SLASH_CROP     = 'crop';

    /**
     * Value for add slash
     */
    const TRAILING_SLASH_ADD      = 'add';

    /**
     * Value for do nothing
     */
    const TRAILING_SLASH_DEFAULT  = 'default';


    protected $_helperData;


    public function getCanonicalUrl($item = null) {

        $this->_helperData = Mage::helper('mageworx_seobase');

        return $this->_getCanonicalUrl($item);
    }

    abstract protected function _getCanonicalUrl($item = null);

    /**
     * Crop or add trailing slash
     *
     * @param string $url
     * @param bool $isHomePage
     * @return string
     */
    public function trailingSlash($url, $isHomePage = false)
    {
        if ($isHomePage) {
            $trailingSlash = $this->_helperData->getTrailingSlashForHomePage();
        } else {
            $trailingSlash = $this->_helperData->getTrailingSlashAction();
        }

        if ($trailingSlash == self::TRAILING_SLASH_ADD) {
            $url        = rtrim($url);
            if(strpos($url, '?') === false){
                $extensions = array('rss', 'html', 'htm', 'xml', 'php');
                if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                    $url.= '/';
                }
            }
        }
        elseif ($trailingSlash == self::TRAILING_SLASH_CROP) {
            $url = rtrim(rtrim($url), '/');
        }

        return $url;
    }

    /**
     * Check if cancel adding canonical URL by config settings
     *
     * @return bool
     */
    protected function isCancelCanonical()
    {
        if (Mage::helper('mageworx_seobase')->isCanonicalUrlEnabled()) {
            $helperData = Mage::helper('mageworx_seobase');
            return in_array($helperData->getCurrentFullActionName(), $helperData->getCanonicalIgnorePages());
        }
        return true;
    }

    /**
     * Prepare ULR to output
     *
     * @param string $url
     * @return string
     */
    public function renderUrl($url)
    {
        return htmlspecialchars($this->trailingSlash($url), ENT_COMPAT, 'UTF-8', false);
    }

    protected function _cropGetParameters($url)
    {
        if (strpos($url, '?') !== false) {
            list($cropedUrl) = explode('?', $url);
            return $cropedUrl;
        }
        return $url;
    }

    protected function _deleteSortParameters($url, $toolbar)
    {
    	if (is_object($toolbar)) {
    		$orderVarName     = $toolbar->getOrderVarName();
			$directionVarName = $toolbar->getDirectionVarName();
			$modeVarName      = $toolbar->getModeVarName();
    	}

        $orderVarName     = (!empty($orderVarName)) ? $orderVarName : 'order';
        $directionVarName = (!empty($directionVarName)) ? $directionVarName : 'dir';
        $modeVarName      = (!empty($modeVarName)) ? $modeVarName : 'mode';

        return $this->_deleteParametrs($url, array($orderVarName, $directionVarName, $modeVarName));
    }

    protected function _deleteLimitParameter($url, $toolbar)
    {
        $limitVarName = $toolbar->getLimitVarName() ? $toolbar->getLimitVarName() : 'limit';

        return $this->_deleteParametrs($url, array($limitVarName));
    }

    protected function _addLimitAllToUrl($url, $toolbar)
    {
        $limitVarName = $toolbar->getLimitVarName() ? $toolbar->getLimitVarName() : 'limit';

        if (strpos($url, '?') !== false) {
            $url = $url . '&' . $limitVarName . '=all';
        }
        else {
            $url = $url . '?' . $limitVarName . '=all';
        }
        return $url;
    }

    protected function _deleteParametrs($url, array $cropParams)
    {
        $parseUrl = parse_url($url);

        if (empty($parseUrl['query'])) {
            return $url;
        }

        $params = array();
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

    public function _deletePagerParameter($url, $toolbar)
    {
        $pageVarName = $toolbar->getPageVarName();
        $url         = $this->_deleteParametrs($url, array($pageVarName));

        return $url;
    }

}