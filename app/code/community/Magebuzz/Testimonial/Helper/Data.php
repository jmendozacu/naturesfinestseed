<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */
class Magebuzz_Testimonial_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getFormUrl() {
		return $this->_getUrl('testimonial/index/check'); 	
	}
	
	public function getBack() {
		return $this->_getUrl('testimonial/index'); 	
	}
	public function getMaxTestimonialsOnSidebar() {
		return (int)Mage::getStoreConfig('testimonial/general_option/max_testimonials_sidebar');
	}
}