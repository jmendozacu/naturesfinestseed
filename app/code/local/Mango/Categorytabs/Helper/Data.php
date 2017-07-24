<?php

class Mango_Categorytabs_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Retrieve customer groups collection
     *
     * @return Mage_Customer_Model_Entity_Group_Collection
     */
    public function getGroupsArray() {
        if (empty($this->_groups)) {
            
            $_asid = Mage::getModel('catalog/category')->getResource()->getEntityType()->getDefaultAttributeSetId();
            
            $this->_groups = Mage::getModel('eav/entity_attribute_group')->getResourceCollection()
                    ->addFieldToFilter('attribute_set_id', array('eq' => $_asid))
                    ->load();
        }


        //print_r($this->_groups);

        $info = array();

        foreach ($this->_groups as $id => $_data) {

            $info[] = array("value" => $_data->getId(), "label" => $_data->getAttributeGroupName());
        }

        return $info;

        // return $this->_groups;
    }

}
