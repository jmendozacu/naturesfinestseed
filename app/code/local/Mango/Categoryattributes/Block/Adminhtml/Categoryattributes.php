<?php
class Mango_Categoryattributes_Block_Adminhtml_Categoryattributes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_categoryattributes';
    $this->_blockGroup = 'categoryattributes';
    $this->_headerText = Mage::helper('categoryattributes')->__('Categories Attributes');
    $this->_addButtonLabel = Mage::helper('categoryattributes')->__('Add Category Attribute');
    parent::__construct();
  }
}