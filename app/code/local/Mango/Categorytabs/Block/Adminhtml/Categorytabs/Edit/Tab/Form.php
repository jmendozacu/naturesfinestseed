<?php

class Mango_Categorytabs_Block_Adminhtml_Categorytabs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('categorytabs_form', array('legend'=>Mage::helper('categorytabs')->__('Item information')));
     
      
      $_info = Mage::registry('categorytabs_data');
      
      $_disable_name = false;
      if($_info->getId() && $_info->getId()< 10) $_disable_name = true; /*disable system tabs changing name*/
      
      
      
      $fieldset->addField('attribute_group_name', 'text', array(
          'label'     => Mage::helper('categorytabs')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'attribute_group_name',
          'disabled' => $_disable_name
      ));
      
      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('categorytabs')->__('Position'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'sort_order',
      ));

     
      if ( Mage::getSingleton('adminhtml/session')->getCategorytabsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCategorytabsData());
          Mage::getSingleton('adminhtml/session')->setCategorytabsData(null);
      } elseif ( Mage::registry('categorytabs_data') ) {
          $form->setValues(Mage::registry('categorytabs_data')->getData());
      }
      return parent::_prepareForm();
  }
}