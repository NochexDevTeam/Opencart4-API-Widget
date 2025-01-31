<?php
// Nochex via form will work for both simple "Seller" account and "Merchant" account holders
// Nochex via APC maybe only avaiable to "Merchant" account holders only - site docs a bit vague on this point
namespace Opencart\Catalog\Controller\Extension\Nochexapi\Payment;

class Callback extends \Opencart\System\Engine\Controller { 

	public function index() {
		 
		//$logger = new Log('nochex.log');
		
		$this->load->language('extension/nochexapi/payment/nochexapi');

		if (isset($this->request->get['method']) && $this->request->get['method'] == 'decline') {
			$this->session->data['error'] = $this->language->get('error_declined');

			$this->response->redirect($this->url->link('checkout/cart'));
		}

		if (isset($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
		} else {
			$order_id = $this->request->get['order'];
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if (!$order_info) {
			$this->session->data['error'] = $this->language->get('error_no_order');

			$this->response->redirect($this->url->link('checkout/cart'));
		}

		// Fraud Verification Step.
		$request = '';

		foreach ($this->request->post as $key => $value) {
			$request .= '&' . $key . '=' . urlencode(stripslashes($value));
		}

			
		if(isset($this->request->post['optional_1']) == "Enabled"){

		$url = "https://secure.nochex.com/callback/callback.aspx";
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		if($_POST["transaction_status"] == "100"){
			$testStatus = "Test";
			
			$this->model_checkout_order->addHistory($order_id, 16, "This transaction was a TEST, please check this was intended and test mode has not been accidentally enabled");				
			$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
		}else{
		$testStatus = "Live";
		}
		
		
		if ($order_info['total'] != $_POST["gross_amount"]){				
			$this->model_checkout_order->addHistory($order_id, 16, "Paid Amount does not match the order total, please check before sending out or supplying goods and services");		
			$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
		}
		
		if ($response=="AUTHORISED") {
			
			$Msg = "<ul style=\"list-style:none;\"><li>Callback: " . $response . "</li>";			
			$Msg .= "<li>Transaction Status: " . $testStatus . "</li>";			
			$Msg .= "<li>Transaction ID: ".$_POST["transaction_id"] . "</li>";
			$Msg .= "<li>Payment Received From: ".$_POST["email_address"] . "</li>";			
			$Msg .= "<li>Total Paid: ".$_POST["gross_amount"] . "</li></ul>";	
		
			$this->model_checkout_order->addHistory($order_id, $this->config->get('payment_nochexapi_order_status_id'), $Msg);
			
			
		} else {
				
			$Msg = "<ul style=\"list-style:none;\"><li>Callback: " . $response . "</li>";			
			$Msg .= "<li>Transaction Status: " . $testStatus . "</li>";			
			$Msg .= "<li>Transaction ID: ".$_POST["transaction_id"] . "</li>";
			$Msg .= "<li>Payment Received From: ".$_POST["email_address"] . "</li>";			
			$Msg .= "<li>Total Paid: ".$_POST["gross_amount"] . "</li></ul>";	
			
			$this->model_checkout_order->addHistory($order_id, $this->config->get('config_order_status_id'), $Msg);
		}
		
		// Since it returned, the customer should see success.
		// It's up to the store owner to manually verify payment.
		$this->response->redirect($this->url->link('checkout/success', '', 'SSL'));


}else{
		ini_set("SMTP","mail.nochex.com" );
		
		$url = "https://secure.nochex.com/apc/apc.aspx";

		// Curl code to post variables back
		$ch = curl_init(); // Initialise the curl tranfer
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request); // Set POST fields
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60); // set connection time out variable - 60 seconds	
		$output = curl_exec($ch); // Post back
		curl_close($ch);

		
		
		if( strstr($output, 'AUTHORISED') !== false ) {
		$Msg = "APC was AUTHORISED, and this was a " . $_POST['status'] . " transaction.";
			$this->model_checkout_order->addHistory($order_id, $this->config->get('payment_nochexapi_order_status_id'), $Msg);
			
		} else {
			$Msg = "APC was DECLINED, and this was a " . $_POST['status'] . " transaction.". $output;
			$this->model_checkout_order->addHistory($order_id, $this->config->get('config_order_status_id'), $Msg);
		}

		// Since it returned, the customer should see success.
		// It's up to the store owner to manually verify payment.
		 $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
		
}
	
	}

}
