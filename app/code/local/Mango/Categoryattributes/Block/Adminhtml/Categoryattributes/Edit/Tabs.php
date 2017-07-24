<?php

class Mango_Categoryattributes_Block_Adminhtml_Categoryattributes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('categoryattributes_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('categoryattributes')->__('Category Attribute'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('categoryattributes')->__('Attribute Information'),
          'title'     => Mage::helper('categoryattributes')->__('Attribute Information'),
          'content'   => $this->getLayout()->createBlock('categoryattributes/adminhtml_categoryattributes_edit_tab_form')->toHtml(),
           'active'    => true
      ));
      
        $model = Mage::registry('entity_attribute');
      
      
      $this->addTab('labels', array(
            'label'     => Mage::helper('categoryattributes')->__('Manage Label / Options'),
            'title'     => Mage::helper('categoryattributes')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('categoryattributes/adminhtml_categoryattributes_edit_tab_options')->toHtml(),
        ));
      
     
      return parent::_beforeToHtml();
  }
}