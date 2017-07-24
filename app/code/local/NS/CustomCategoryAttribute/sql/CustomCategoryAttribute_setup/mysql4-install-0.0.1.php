<?php
$productEntityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->addAttribute($productEntityTypeId, 'cat_body_text', array(
    'group'         => 'General',
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Body',
    'backend'       => '',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => true,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
?>