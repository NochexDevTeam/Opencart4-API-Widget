<?php
// Nochex via form will work for both simple "Seller" account and "Merchant" account holders
// Nochex via APC maybe only avaiable to "Merchant" account holders only - site docs a bit vague on this point
namespace Opencart\Catalog\Controller\Extension\Nochexapi\Payment;

class Nochexapi extends \Opencart\System\Engine\Controller { 

	public function index() {
		$this->load->language('extension/nochexapi/payment/nochexapi');
		$products = $this->cart->getProducts();
		setlocale(LC_MONETARY, 'en_GB');	
		$data['button_confirm'] = $this->language->get('button_confirm');

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['action'] = 'https://secure.nochex.com/default.aspx';

		// Nochex minimum requirements
		// The merchant ID is usually your Nochex registered email address but can be altered for "Merchant" accounts see below
		if ($this->config->get('payment_nochexapi_email') != $this->config->get('payment_nochexapi_merchant')) {
			// This MUST be changed on your Nochex account!!!!
			$data['merchant_id'] = $this->config->get('payment_nochexapi_merchant');
		} else {
			$data['merchant_id'] = $this->config->get('payment_nochexapi_email');
		}	
	
		$data['api_key'] = $this->config->get('payment_nochexapi_merchantapi');
	
		// XML Item Collection / Description
		
		if($this->config->get('payment_nochexapi_xmlcollection') == 1){
		
		$xmlCollection = "<items>";
		
		foreach ($products as $product) {
			$xmlCollection .= "<item><id>".$product['product_id']."</id><name>".preg_replace("/[^A-Za-z0-9  ]/", "", $product['name'])."</name><description>".preg_replace("/[^A-Za-z0-9  ]/", "", $product['model'])."</description><quantity>".$product['quantity']."</quantity><price>" . $product['price'] . "</price></item>";
		}
		
		$xmlCollection .= "</items>";
		
		
		$description = "Order :" . $this->session->data['order_id'] ;
		
		}else{
		$xmlCollection = "";
		$description = "Product Details: ";
		
		foreach ($products as $product) {
			$description .= " Product ID: ".$product['product_id'].", Product Name: ".preg_replace("/[^A-Za-z0-9  ]/", "", $product['name']).", Product Description: ".preg_replace("/[^A-Za-z0-9  ]/", "", $product['model']).", Product Quantity: ".$product['quantity'].", Product Price: &pound;" . $product['price'] . "   ";
		}
		
		$description .= ".";
		}
		
		$data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], false, false);
		
		$data['order_id'] = $this->session->data['order_id'];
		$data['description'] = $description;
		
		if( !empty($order_info['payment_firstname'])){
		
		$data['billing_fullname'] = preg_replace("/[^A-Za-z  ]/", "", $order_info['payment_firstname']) . ' ' . preg_replace("/[^A-Za-z  ]/", "", $order_info['payment_lastname']);
			
		if (isset($order_info['payment_address_2'])) {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_1']) . " " . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_2']);
		} else {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_address_1']);
		}
		
		$data['billing_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_city']);
		$data['billing_postcode'] = $order_info['payment_postcode'];
		} else {
		
		
		$data['billing_fullname'] = preg_replace("/[^A-Za-z  ]/", "", $order_info['shipping_firstname']) . ' ' . preg_replace("/[^A-Za-z  ]/", "", $order_info['shipping_lastname']);
			
		if (isset($order_info['payment_address_2'])) {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']) . " " . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_2']);
		} else {
			$data['billing_address']  = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']);
		}
		
		$data['billing_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['shipping_city']);
		$data['billing_postcode'] = $order_info['shipping_postcode'];
		
		}
		
		if ($this->cart->hasShipping()) {
			$data['delivery_fullname'] = preg_replace("/[^A-Za-z ]/", "", $order_info['shipping_firstname']) . ' ' . preg_replace("/[^A-Za-z  ]/", "", $order_info['shipping_lastname']);

			if (isset($order_info['shipping_address_2'])) {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']) . " " . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_2']);
			} else {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['shipping_address_1']);
			}
			$data['delivery_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['shipping_city']);
			$data['delivery_postcode'] = $order_info['shipping_postcode'];
		} else {
			$data['delivery_fullname'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_firstname']) . ' ' . preg_replace("/[^A-Za-z0-9  ]/", "", $order_info['payment_lastname']);

			if (isset($order_info['payment_address_2'])) {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['payment_address_1']) . " " . preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['payment_address_2']);
			} else {
				$data['delivery_address'] = preg_replace("/[^A-Za-z0-9 ]/", "", $order_info['shipping_address_1']);
			}
			$data['delivery_city'] = preg_replace("/[^A-Za-z ]/", "", $order_info['payment_city']);
			$data['delivery_postcode'] = $order_info['payment_postcode'];
		}
		
		if($this->config->get('payment_nochexapi_hide') == 1){
		$data['hide_billing_details'] = "true";		
		}
		$data['xmlcollection'] = $xmlCollection;
		
		 
		$data['email_address'] = $order_info['email'];
		$data['customer_phone_number']= preg_replace("/[^0-9]/", "",$order_info['telephone']);
		$data['test'] = $this->config->get('payment_nochexapi_test');
		$data['success_url'] = $this->url->link('checkout/success', '', true);
		$data['cancel_url'] = $this->url->link('checkout/checkout', '', true);
		$data['declined_url'] = $this->url->link('extension/nochexapi/payment/callback', 'method=decline', true);
		$data['callback_url'] = $this->url->link('extension/nochexapi/payment/callback', 'order=' . $this->session->data['order_id'], true);
				
		$data['optional_1'] = "Enabled";
		
		/*if($this->config->get('payment_nochexapi_debug')==1){
		
		$logger->write('Success URL: '. $data['success_url']);
		$logger->write('Cancel URL: '. $data['cancel_url']);
		$logger->write('Declined URL: '. $data['declined_url']);
		$logger->write('APC / Callback URL: '. $data['callback_url']);
		
		}*/
				
		return $this->load->view('extension/nochexapi/payment/nochexapi', $data);
	}

}
