<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */
class Magebuzz_Testimonial_Model_Session extends Mage_Core_Model_Session_Abstract {

	public function __construct() {
		$this->init('testimonial');
	}

}
