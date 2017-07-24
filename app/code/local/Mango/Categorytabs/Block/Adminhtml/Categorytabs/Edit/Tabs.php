<?php

class Mango_Categorytabs_Block_Adminhtml_Categorytabs_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('categorytabs_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('categorytabs')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('categorytabs')->__('Item Information'),
          'title'     => Mage::helper('categorytabs')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('categorytabs/adminhtml_categorytabs_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}