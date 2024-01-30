<?php

namespace Opencart\Catalog\Model\Extension\Nochexapi\Payment;
 

class Nochexapi extends \Opencart\System\Engine\Model {

	public function getMethods(array $address): array {
		$this->load->language('extension/nochexapi/payment/nochexapi');
 

		if ($this->cart->hasSubscription()) {
			$status = false;
		} elseif (!$this->cart->hasShipping()) {
			$status = false;
		} elseif (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_nochexapi_geo_zone_id')) {
			$status = true;
		} else {
		
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$this->config->get('payment_nochexapi_geo_zone_id') . "' AND `country_id` = '" . (int)$address['country_id'] . "' AND (`zone_id` = '" . (int)$address['zone_id'] . "' OR `zone_id` = '0')");
			
			if ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}		
		}

		$method_data = [];

		if ($status) {
		
			$option_data['nochexapi'] = [
				'code' => 'nochexapi.nochexapi',
				'name' => $this->language->get('heading_title')
			];
			
			$method_data = [
				'code'       => 'nochexapi',
				'name'      => "Pay by Card",
				'option'     => $option_data,
				'sort_order' => '0',
			];
		}
		
		return $method_data;
	}
}