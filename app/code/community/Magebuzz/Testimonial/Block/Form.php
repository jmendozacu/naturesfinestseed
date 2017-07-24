<?php
/**
 * @category  Magebuzz
 * @package   Magebuzz_Testimonial
 * @version   0.1.5
 * @copyright Copyright (c) 2012-2015 http://www.magebuzz.com
 * @license   http://www.magebuzz.com/terms-conditions/
 */
class Magebuzz_Testimonial_Block_Form extends Mage_Core_Block_Template {
  public function _prepareLayout() {
    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('testimonial')->__('Testimonial Form'));
    $this->setTemplate('testimonial/form.phtml');
    return parent::_prepareLayout();
  }
  
  public function isCustomerLoggedIn() {
    return Mage::getSingleton('customer/session')->isLoggedIn();
    }
  
  public function getCustomer () {
    return Mage::getSingleton('customer/session')->getCustomer();
  }
  
  public function getBack() {
    return $this->helper('testimonial')->getBack();
  }
}