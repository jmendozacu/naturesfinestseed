<?php

class Mage_DilverySign_Model_Observer 
{
    public function saveDeliverySignature($event)
    {
		die('there');
        $quote = $event->getSession()->getQuote();
        $quote->setData('delivery_signature', $event->getRequestModel()->getPost('delivery_signature'));

        return $this;
    }
}