<?php

class Mango_Categorytabs_Model_Mysql4_Categorytabs_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categorytabs/categorytabs');
    }
}