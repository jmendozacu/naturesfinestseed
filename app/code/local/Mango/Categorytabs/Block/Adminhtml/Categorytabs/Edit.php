<?php

class Mango_Categorytabs_Block_Adminhtml_Categorytabs_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'categorytabs';
        $this->_controller = 'adminhtml_categorytabs';
        
        $this->_updateButton('save', 'label', Mage::helper('categorytabs')->__('Save Item'));
        
        
         $_info = Mage::registry('categorytabs_data');
      
      $_disable_name = false;
      if($_info->getId() && $_info->getId()> 10){ /*disable system tabs changing name*/
       $this->_updateButton('delete', 'label', Mage::helper('categorytabs')->__('Delete Item'));
      }else{
          $this->_removeButton('delete');
      }
      
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('categorytabs_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'categorytabs_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'categorytabs_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('categorytabs_data') && Mage::registry('categorytabs_data')->getId() ) {
            return Mage::helper('categorytabs')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('categorytabs_data')->getAttributeGroupName()));
        } else {
            return Mage::helper('categorytabs')->__('Add Item');
        }
    }
}