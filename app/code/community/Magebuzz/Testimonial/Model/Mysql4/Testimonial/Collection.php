<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */

class Magebuzz_Testimonial_Model_Mysql4_Testimonial_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
      parent::_construct();
      $this->_init('testimonial/testimonial');
    }
}