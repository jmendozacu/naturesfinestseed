<?php

class Mango_Categoryattributes_Block_Adminhtml_Categoryattributes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_blockGroup = 'categoryattributes';
        $this->_controller = 'adminhtml_categoryattributes';

        parent::__construct();
                 
        $this->_updateButton('save', 'label', Mage::helper('categoryattributes')->__('Save Item'));
        if (! Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('categoryattributes')->__('Delete Attribute'));
        }
        
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('categoryattributes_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'categoryattributes_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'categoryattributes_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('entity_attribute') && Mage::registry('entity_attribute')->getId() ) {
            return Mage::helper('categoryattributes')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('entity_attribute')->getFrontendLabel()));
        } else {
            return Mage::helper('categoryattributes')->__('Add Item');
        }
    }
    
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()
            ->createBlock('eav/adminhtml_attribute_edit_js')->setTemplate("categoryattributes/attribute/js.phtml")->toHtml();
        return $html.$jsScripts ;
    }
    
}