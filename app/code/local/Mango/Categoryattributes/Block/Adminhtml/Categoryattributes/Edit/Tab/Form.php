<?php

class Mango_Categoryattributes_Block_Adminhtml_Categoryattributes_Edit_Tab_Form extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
  
    /**
     * Adding product form elements for editing attribute
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            /*array(
                'value' => 'price',
                'label' => Mage::helper('catalog')->__('Price')
            ),*/
            array(
                'value' => 'image',
                'label' => Mage::helper('catalog')->__('Image')
            )
        );
        /*if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('catalog')->__('Gallery')
            );
        }*/

      /*  $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_product_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);*/

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        /*$scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );*/

       /* if ($attributeObject->getAttributeCode() == 'status' || $attributeObject->getAttributeCode() == 'tax_class_id') {
            unset($scopes[Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'note'  => Mage::helper('catalog')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');*/

      
    

        // frontend properties fieldset
       // $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));

      /*  $fieldset->addField('is_searchable', 'select', array(
            'name'     => 'is_searchable',
            'label'    => Mage::helper('catalog')->__('Use in Quick Search'),
            'title'    => Mage::helper('catalog')->__('Use in Quick Search'),
            'values'   => $yesnoSource,
        ));*/

         $fieldset->addField('note', 'text', array(
            'name' => 'note',
            'label' => Mage::helper('catalog')->__('Comment'),
            'title' => Mage::helper('catalog')->__('Comment'),
            'value' => $attributeObject->getNote(),
        ));
        
        $_tab_options = Mage::helper('categorytabs')->getGroupsArray();
        //Mage_Eav_Model_Entity_Attribute_Group
        
        $fieldset->addField('attribute_group_id', 'select', array(
            'name' => 'attribute_group_id',
            'label' => Mage::helper('catalog')->__('Tab'),
            'title' => Mage::helper('catalog')->__('Tab'),
            //'value' => $attributeObject->getAttributeGroupId(),
            'values'   => $_tab_options,
        ));
        
        
        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('catalog')->__('Position'),
            'title' => Mage::helper('catalog')->__('Position'),
            'value' => $attributeObject->getSortOrder(),
        ));
        
        
        
        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'title' => Mage::helper('catalog')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        
      

        //$form->getElement('apply_to')->setSize(5);

        /*if ($applyTo = $attributeObject->getApplyTo()) {
            $applyTo = is_array($applyTo) ? $applyTo : explode(',', $applyTo);
            $form->getElement('apply_to')->setValue($applyTo);
        } else {
            $form->getElement('apply_to')->addClass('no-display ignore-validate');
        }*/

        // define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
            ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
            ->addFieldMap("frontend_input", 'frontend_input_type')
            ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
            //->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );

        Mage::dispatchEvent('adminhtml_catalog_category_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        return $this;
    }

    /**
     * Retrieve additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply'         => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply'),
        );
    }
    
   
    
  }