<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_SeoBase_Model_Hreflang_Abstract extends Mage_Core_Model_Abstract
{
    abstract protected function _getHreflangUrls();

    public function getHreflangUrls()
    {
        $urls = $this->_getHreflangUrls();
        return $this->_cropUrlParams($urls);
    }

    protected function _cropUrlParams($urls)
    {
        if (!$urls) {
            return null;
        }

        foreach ($urls as $code => $url) {
            $pos = strpos($url, '?');
            $urls[$code] = $pos ? substr($urls[$code], 0, $pos) : $urls[$code];
        }
        return $urls;
    }
}