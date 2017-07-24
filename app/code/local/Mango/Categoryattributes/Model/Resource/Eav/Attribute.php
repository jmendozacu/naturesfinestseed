<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer attribute model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */



class Mango_Categoryattributes_Model_Resource_Eav_Attribute extends Mage_Catalog_Model_Resource_Eav_Attribute {

    protected $_entityTypeId;







    public function getResourceCollection() {


        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Category::ENTITY)->getTypeId();

        if (empty($this->_resourceCollectionName)) {

            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }



        //echo $this->_resourceCollectionName . "---";
        //echo $this->_getResource();

        $resource_collection = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());

        $resource_collection
                ->getSelect()
                ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
                ->join(
                        array('additional_table' => $this->getTable('catalog/eav_attribute')), 'additional_table.attribute_id = main_table.attribute_id'
                )
                ->join(
                        array('tabs' => $this->getTable('eav/entity_attribute')), 'tabs.attribute_id = main_table.attribute_id'
                )
                ->join(
                        array('tabs_details' => $this->getTable('eav/attribute_group')), 'tabs_details.attribute_group_id = tabs.attribute_group_id', array("attribute_group_name")
                )
                ->where('main_table.entity_type_id = ?', $this->_entityTypeId);




        ;



        // echo $resource_collection->getSelect();
        //$resource_collection->left */

        return $resource_collection;
    }

}
