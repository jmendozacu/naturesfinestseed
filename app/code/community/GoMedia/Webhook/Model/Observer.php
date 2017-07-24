<?php

/**
 * Observer to handle event
 * Sends JSON data to URL specified in extensions admin settings
 *
 * @author Chris Sohn (www.gomedia.co.za)
 * @copyright  Copyright (c) 2015 Go Media
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class GoMedia_Webhook_Model_Observer {

    /**
     * Used to ensure the event is not fired multiple times
     * http://magento.stackexchange.com/questions/7730/sales-order-save-commit-after-event-triggered-twice
     *
     * @var bool
     */
    private $_processFlag = false;

    /**
     * Posts order
     *
     * @param Varien_Event_Observer $observer
     * @return GoMedia_Webhook_Model_Observer
     */
    public function postOrder($observer) {

        // make sure this has not already run
        if (!$this->_processFlag) {

            /** @var $order Mage_Sales_Model_Order */
            $order = $observer->getEvent()->getOrder();
            $orderStatus = $order->getStatus();
            $url = Mage::getStoreConfig('webhook/order/url', $order['store_id']);
            if (!is_null($orderStatus) && $url) {
                $data = $this->transformOrder($order);
                $response = $this->proxy($data, $url);

                // save comment
                $order->addStatusHistoryComment(
                    'SeedOps -> Server Status: ' . $response->status . " | Response: " . $response->body,
                    false
                );
                $this->_processFlag = true;
                $order->save();
            }
        }
        return $this;
    }


    /**
     * Curl data and return body
     *
     * @param $data
     * @param $url
     * @return stdClass $output
     */
    private function proxy($data, $url) {

        $output = new stdClass();
        $ch = curl_init();
        
        $order_status = '';
		switch ($data['state']) {
			case 'new':
				$order_status = 'CreateOrder';
				break;
			case 'processing':
				$order_status = 'Processing';
				break;				
			case 'complete':
				$order_status = 'MarkOrderShipped';
				break;			
		}
		
		
		$paymnet_used = $data['payment']['method'];		
		if (($order_status=="Processing") && ($paymnet_used=="authorizenet")) {
			$order_status='CreateOrder';
		}		
		if (($order_status=="Processing") && ($paymnet_used=="paypal_express")) {
			$order_status='CreateOrder';
		}
		if (($order_status=="Processing") && ($paymnet_used=="m2epropayment")) {
			$order_status='CreateOrder';
		}		
		
	$request = array
	    ( 'divisionCode' => 'NF'
	    , 'userName' => 'skylar'
	    , 'orderNumber' => $data['increment_id']
	    , 'rush' => 0
	    , 'trackingNum' => ''
	    , 'taxExempt' => ''
	    , 'tax' => $data['tax_amount']
	    , 'termsID' => 6
	    , 'endUseID' => ''
	    , 'shipper' => $data['shipping_method']
	    , 'payment_method' => $data['payment']['method']	    
	    , 'freight' => $data['payment']['shipping_amount']
	    , 'handling' => 0
	    , 'mixing' => 0
	    , 'phytosan' => 0
	    , 'palletCount' => 0
	    , 'palletCharge' => 0
	    , 'customer' => array
	        ( /*'name' => $data['customer_firstname'].' '.$data['customer_lastname'] */
	          'name' => $data['billing_address']['firstname']." ".$data['billing_address']['lastname']
	        , 'customerNumber' => $data['customer_id']
	        , 'taxExemptNumber' => 'UT132333'
	        , 'customerTypeID' => 3450
	        , 'termsID' => 6
	        , 'endUseID' => ''
	        , 'taxExempt' => ''
 	        , 'email' => $data['customer_email']
	        , 'primaryPhone' => $data['billing_address']['telephone']
	        )
	    , 'shipAddress' => array
	        ( 'shipping_name' => $data['shipping_address']['firstname']." ".$data['shipping_address']['lastname']
	        , 'address1' => $data['shipping_address']['street']
	        , 'address2' => ''
	        , 'address3' => ''
	        , 'city' => $data['shipping_address']['city']
	        , 'province' => $data['shipping_address']['region']
	        , 'postalCode' => $data['shipping_address']['postcode']
	        )
	    , 'billAddress' => array
	        ( 'billing_name' => $data['billing_address']['firstname']." ".$data['billing_address']['lastname']
	        , 'address1' => $data['billing_address']['street']
	        , 'address2' => ''
	        , 'address3' => ''
	        , 'city' => $data['billing_address']['city']
	        , 'province' => $data['billing_address']['region']
	        , 'postalCode' => $data['billing_address']['postcode']
	        )
	    );		    

		foreach ($data['line_items'] as $itemce ) {
			$row['sku']	= $itemce['sku'];
			$row['qty'] = $itemce['qty_ordered'];
			$row['price'] = $itemce['price'];
			$request['orderLines'][] = $row;
		}	

		$field['method'] = $order_status;
		$field['orderNumber'] = $data['increment_id'];
		$field['secret'] = 'f4917110-5d95-41a8-bc99-4274ca716ae4'; 
		$field['payload'] = json_encode($request); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $field);        
        curl_setopt($ch, CURLOPT_URL, 'https://seedops.com/api/Update.php' /* $url */);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);               
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");     
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);        

        // ignore cert issues
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);        

        // execute
	    if (($order_status=='CreateOrder') || ($order_status=='MarkOrderShipped')) {
	        $response = curl_exec($ch);
	        $output->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        curl_close($ch);

        // handle response
        
		$output->body = $response.' | Data Sent: '.var_export($field, true);
        //$output->body = $response;

		}
		else {
			$output->status = 'Not send';
			$output->body = 'Only new order and shipment are send - STATUS: '.$order_status.' - STATE: '.$data['state'].' - Payment: '.$data['payment']['method'].' - Quote: '.$data['converting_from_quote'];
		}
		
        return $output;
    }

    /**
     * Transform order into one data object for posting
     */
    /**
     * @param $orderIn Mage_Sales_Model_Order
     * @return mixed
     */
    private function transformOrder($orderIn) {
        $orderOut = $orderIn->getData();
        $orderOut['line_items'] = array();
        foreach ($orderIn->getAllItems() as $item) {
            $orderOut['line_items'][] = $item->getData();
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($orderIn->getCustomerId());
        $orderOut['customer'] = $customer->getData();
        $orderOut['customer']['customer_id'] = $orderIn->getCustomerId();

        /** @var $shipping_address Mage_Sales_Model_Order_Address*/
        $shipping_address = $orderIn->getShippingAddress();
        $orderOut['shipping_address'] = $shipping_address->getData();

        /** @var $shipping_address Mage_Sales_Model_Order_Address*/
        $billing_address = $orderIn->getBillingAddress();
        $orderOut['billing_address'] = $billing_address->getData();

        /** @var $shipping_address Mage_Sales_Model_Order_Payment*/
        $payment = $orderIn->getPayment()->getData();

        // remove cc fields
        foreach ($payment as $key => $value) {
            if (strpos($key, 'cc_') !== 0) {
                $orderOut['payment'][$key] = $value;
            }
        }

        /** @var $orderOut Mage_Core_Model_Session */
        $session = Mage::getModel('core/session');
        $orderOut['visitor'] = $session->getValidatorData();
        return $orderOut;
    }
}