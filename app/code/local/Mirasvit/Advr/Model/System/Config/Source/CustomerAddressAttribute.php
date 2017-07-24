<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.21
 * @build     712
 * @copyright Copyright (C) 2016 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_Advr_Model_System_Config_Source_CustomerAddressAttribute extends Varien_Object
{
    public function toOptionArray()
    {
        $values = array();

        foreach ($this->getCollection() as $attr) {
            if ($attr->getFrontendLabel() && $attr->getAttributeCode()) {
                $values[] = array(
                    'value' => $attr->getAttributeCode(),
                    'label' => $attr->getFrontendLabel(),
                );
            }
        }

        return $values;
    }

    public function toOptionHash()
    {
        $values = array();

        foreach ($this->getCollection() as $attr) {
            if ($attr->getFrontendLabel() && $attr->getAttributeCode()) {
                $values[$attr->getAttributeCode()] = $attr->getFrontendLabel();
            }
        }

        return $values;
    }

    protected function getCollection()
    {
        return Mage::getModel('customer/address')->getAttributes();
    }
}
