<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_Canonical_Simple extends MageWorx_SeoBase_Model_Canonical_Abstract
{
    protected function _getCanonicalUrl($item = null)
    {
        return $this->trailingSlash($this->_cropGetParameters(Mage::helper('core/url')->getCurrentUrl()));
    }

}