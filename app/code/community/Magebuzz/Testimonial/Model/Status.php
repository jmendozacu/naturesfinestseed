<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */
class Magebuzz_Testimonial_Model_Status extends Varien_Object {

  const STATUS_ENABLED	= 1;
  const STATUS_DISABLED	= 2;
  const STATUS_PENDING  = 3;

    static public function getOptionArray() {
      return array(
        self::STATUS_ENABLED    => Mage::helper('testimonial')->__('Approved'),
        self::STATUS_DISABLED   => Mage::helper('testimonial')->__('Not Approved'),
        self::STATUS_PENDING   => Mage::helper('testimonial')->__('Pending')
        );
    }
	
}