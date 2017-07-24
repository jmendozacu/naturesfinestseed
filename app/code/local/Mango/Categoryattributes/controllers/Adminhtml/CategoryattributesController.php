<?php

class Mango_Categoryattributes_Adminhtml_CategoryattributesController extends Mage_Adminhtml_Controller_action {

    protected $_entityTypeId;

    public function preDispatch() {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Category::ENTITY)->getTypeId();
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('categoryattributes/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Attributes Manager'), Mage::helper('adminhtml')->__('Attributes Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('attribute_id');
        $model = Mage::getModel('categoryattributes/category_attribute')
                ->setEntityTypeId($this->_entityTypeId);

        /* $model = Mage::getResourceModel('categoryattributes/category_attribute_collection')
          ->addVisibleFilter(); */





        if ($id) {
            $model->load($id);
        }

        //print_r($model);

        /* $collection = Mage::getResourceModel('categoryattributes/category_attribute_collection')
          ->addVisibleFilter(); */

        /* $collection->getSelect()
          ->where('main_table.is_user_defined = ?', 1); */





        if ($model->getId() || $id == 0) {

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('This attribute cannot be edited.'));
                $this->_redirect('*/*/');
                return;
            }



            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('entity_attribute', $model);

            $this->loadLayout();
            $this->_setActiveMenu('categoryattributes/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Attributes Manager'), Mage::helper('adminhtml')->__('Attributes Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('categoryattributes/adminhtml_categoryattributes_edit'))
                    ->_addLeft($this->getLayout()->createBlock('categoryattributes/adminhtml_categoryattributes_edit_tabs'));

            $this->renderLayout();

            //          $this->_initAction()
            //->_addContent($this->getLayout()->createBlock('amcustomerattr/adminhtml_customer_attribute_edit')->setData('action', $this->getUrl('*/catalog_product_attribute/save')))
            //->_addLeft($this->getLayout()->createBlock('amcustomerattr/adminhtml_customer_attribute_edit_tabs'))
            //->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('categoryattributes')->__('Attribute does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function validateAction() {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalog')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data) {
        if ($data) {
            /** @var $helperCatalog Mage_Catalog_Helper_Data */
            $helperCatalog = Mage::helper('catalog');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $helperCatalog->escapeHtml($value);
                }
            }
            //options
            if (!empty($data['option']['value'])) {
                foreach ($data['option']['value'] as &$options) {
                    foreach ($options as &$label) {
                        $label = $helperCatalog->escapeHtml($label);
                    }
                }
            }
            //default value
            if (!empty($data['default_value'])) {
                $data['default_value'] = $helperCatalog->escapeHtml($data['default_value']);
            }
            if (!empty($data['default_value_text'])) {
                $data['default_value_text'] = $helperCatalog->escapeHtml($data['default_value_text']);
            }
            if (!empty($data['default_value_textarea'])) {
                $data['default_value_textarea'] = $helperCatalog->escapeHtml($data['default_value_textarea']);
            }
        }
        return $data;
    }

    public function saveAction() {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');

            $redirectBack = $this->getRequest()->getParam('back', false);
            /* @var $model Mage_Catalog_Model_Entity_Attribute */
            $model = Mage::getModel('catalog/resource_eav_attribute');
            /* @var $helper Mage_Catalog_Helper_Product */
            $helper = Mage::helper('catalog/category');

            $id = $this->getRequest()->getParam('attribute_id');

            $addToSet = false;

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                            $helper->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.'));
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }


            //validate frontend_input
            if (isset($data['frontend_input']) && $data['frontend_input'] != "image") {
                /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                            Mage::helper('catalog')->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                            Mage::helper('catalog')->__('This attribute cannot be updated.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
                $data['is_global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
            } else {
                /**
                 * @todo add to helper and specify all relations for properties
                 */
                if ('multiselect' == $data['frontend_input']) {
                    //$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                    //$data['backend_model'] = 'eav/entity_attribute_source_table'; //$helper->getAttributeBackendModelByInputType($data['frontend_input']);
                    $data['source_model'] = 'eav/entity_attribute_source_table';
                    $data['backend_model'] = 'eav/entity_attribute_backend_array';
                }

                if ('image' == $data['frontend_input'] || 'media_image' == $data['frontend_input']) {
                    //$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                    $data['backend_model'] = 'catalog/category_attribute_backend_image'; //$helper->getAttributeBackendModelByInputType($data['frontend_input']);
                    $data['backend_type'] = 'varchar'; //$helper->getAttributeBackendModelByInputType($data['frontend_input']);//
                    //$data['source_model'] = 'eav/entity_attribute_source_table';
                }

                //catalog/category_attribute_backend_image


                if ('boolean' == $data['frontend_input']) {
                    //data['frontend_input'] = 'select';
                    //$data['source_model'] = 'eav/entity_attribute_source_boolean';
                    $data['source_model'] = 'eav/entity_attribute_source_boolean';
                    //$data['frontend_input'] = 'select';
                    // $data['backend_model'] = 'eav/entity_attribute_source_boolean';
                }

                $addToSet = true;

                //$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                //$data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }

            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }

            if (!isset($data['sort_order'])) {
                $data['sort_order'] = 0;
            }

            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }



            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if (!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }


            $data['is_global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;





            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }


            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('attribute_group_id')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('attribute_group_id'));

                $data["attribute_group_id"] = $this->getRequest()->getParam('attribute_group_id');
            }




            try {
                $model->save();


                // adding attribute to set
                $attrSetId = Mage::getModel('catalog/category')->getResource()->getEntityType()->getDefaultAttributeSetId();
                $setup = new Mage_Eav_Model_Entity_Setup();
                if ($addToSet) {
                    $setup->addAttributeToSet('catalog_category', $attrSetId, $data["attribute_group_id"], $model->getAttributeCode(), $data['sort_order']);
                } else {
                    $setup->updateAttribute($this->_entityTypeId, $id, "attribute_group_id", $data["attribute_group_id"], $data['sort_order']);

                    $setup->updateTableRow('eav/entity_attribute', 'attribute_id', $id, 'attribute_group_id', $data["attribute_group_id"]
                    );

                    //$setup->updateAttributeGroup($this->_entityTypeId, $attrSetId, $data["attribute_group_id"], "attribute_group_id", $data["attribute_group_id"]  );
                }

                $session->addSuccess(
                        Mage::helper('catalog')->__('The product attribute has been saved.'));

                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
                /* if ($this->getRequest()->getParam('popup')) {
                  $this->_redirect('adminhtml/catalog_product/addAttribute', array(
                  'id'       => $this->getRequest()->getParam('product'),
                  'attribute'=> $model->getId(),
                  '_current' => true
                  ));
                  } else */
                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(), '_current' => true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel('catalog/resource_eav_attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('catalog')->__('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('catalog')->__('The product attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('catalog')->__('Unable to find an attribute to delete.'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes');
    }

}