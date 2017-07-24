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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV attribute resource model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Categoryattributes_Model_Mysql4_Category_Attribute extends Mage_Eav_Model_Mysql4_Entity_Attribute
{
    

    /**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityTypeId = (int)Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Category::ENTITY)->getTypeId();
        
           $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            
                    ->where($this->getMainTable().'.'.$field.'=?', $value)
            
                   ->join(
                array('additional_table' => $this->getTable('catalog/eav_attribute')),
                'additional_table.attribute_id = ' .$this->getMainTable(). '.attribute_id'
                )
            ->join(
                array('tabs' => $this->getTable('eav/entity_attribute')),
                'tabs.attribute_id = ' . $this->getMainTable() . '.attribute_id'
                )    
            ->join(
                array('tabs_details' => $this->getTable('eav/attribute_group')),
                'tabs_details.attribute_group_id = tabs.attribute_group_id',
                 array("attribute_group_name", "attribute_group_id")   
                )        
            ->where( $this->getMainTable(). '.entity_type_id = ?', $entityTypeId)
             ->where($this->getMainTable(). '.is_user_defined = ?', 1);
                   
                   

                ;

//echo $select ;
           
           
           //echo $select;

        return $select;
    }


   
}
