<?php
class Mango_Categorytabs_Block_Adminhtml_Categorytabs extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_categorytabs';
    $this->_blockGroup = 'categorytabs';
    $this->_headerText = Mage::helper('categorytabs')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('categorytabs')->__('Add Item');
    parent::__construct();
  }
}