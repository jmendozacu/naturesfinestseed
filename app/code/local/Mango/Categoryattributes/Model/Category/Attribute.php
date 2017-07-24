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
class Mango_Categoryattributes_Model_Category_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    
    
    
    /**
     * 
     * Name of the module
     */
    //const MODULE_NAME = 'Mango_Categoryattributes';

    /**
     * Prefix of model events names
     *
     * @var string
     */
   // protected $_eventPrefix = 'category_entity_attribute';

    /**
     * Prefix of model events object
     *
     * @var string
     */
  //  protected $_eventObject = 'attribute';

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('categoryattributes/category_attribute');
    }
    
    
   /* public function loadCategoryAttributeById( $id ){
        
        return $this->_getResource()->loadAttributeById($id);
        
        
        
        
    }*/
    
    
    
    
    
     public function getResourceCollection() {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }

        //echo $this->_resourceCollectionName . "---";
        //echo $this->_getResource();

        $resource_collection = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());

       /*  $resource_collection
          ->getSelect()
          ->join(
          array('attribute_value' => $this->getResource()->getTable("eav/attribute_option_value")),
          'attribute_value.option_id = main_table.option_id and attribute_value.store_id=0',
          array("value", "eav_option_id"=>"option_id"))
          ->join(
          array('attribute' => $this->getResource()->getTable("eav/attribute_option")),
          'attribute_value.option_id = attribute.option_id',
          array())
          ->join(
          array('attribute_info' => $this->getResource()->getTable("eav/attribute")),
          'attribute.attribute_id = attribute_info.attribute_id and attribute_code="color"',
          array())

          ;*/

          //echo $resource_collection
          //->getSelect();

          //$resource_collection->left */

        return $resource_collection;
    }
    
    
    
    
    
    
    
}
