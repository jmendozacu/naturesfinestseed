<?php

class Mango_Categorytabs_Model_Categorytabs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('categorytabs/categorytabs');
    }
}