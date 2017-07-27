<?php

class Mage_DilverySign_Model_Observer 
{
		public function saveQuoteBefore($evt){

		$quote = $evt->getQuote();

		$post = Mage::app()->getFrontController()->getRequest()->getPost();

		if(isset($post['delivery_signature'])){

		$var = $post['delivery_signature'];
	
		$quote->setDeliverySignature($var);

		}

		}

		public function saveQuoteAfter($evt){

		$quote = $evt->getQuote();
		
		}

		public function loadQuoteAfter($evt){

		$quote = $evt->getQuote();

		}

		public function saveOrderAfter($evt){

		$order = $evt->getOrder();

		$quote = $evt->getQuote();	

		$post = Mage::app()->getFrontController()->getRequest()->getPost();

		if(!empty($quote->getDeliverySignature())){
			$var = $quote->getDeliverySignature();
		$order->setDeliverySignature($var);

		}
		
		if(empty($order->getShippingAddress()->getTelephone())){
			$shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddress()->getId());
			$shippingAddress->setTelephone("801-701-9446");
			
		}
		if(empty($order->getBillingAddress()->getTelephone())){
			$billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddress()->getId());
			$billingAddress->setTelephone("801-701-9446");
		}

		

		}

		public function loadOrderAfter($evt){

		$order = $evt->getOrder();

		}
}