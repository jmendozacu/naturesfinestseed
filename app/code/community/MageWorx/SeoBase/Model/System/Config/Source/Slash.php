<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Slash
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => MageWorx_SeoBase_Model_Canonical_Abstract::TRAILING_SLASH_CROP,
                'label' => Mage::helper('mageworx_seobase')->__('Default')
            ),
            array(
                'value' => MageWorx_SeoBase_Model_Canonical_Abstract::TRAILING_SLASH_ADD,
                'label' => Mage::helper('mageworx_seobase')->__('Add')
            ),
            array(
                'value' => MageWorx_SeoBase_Model_Canonical_Abstract::TRAILING_SLASH_DEFAULT,
                'label' => Mage::helper('mageworx_seobase')->__('Crop')
            ),
        );
    }
}
