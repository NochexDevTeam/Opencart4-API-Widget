<?php

namespace Opencart\Admin\Controller\Extension\Nochexapi\Payment;

class Nochexapi extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/nochexapi/payment/nochexapi');

		$this->document->setTitle("Nochex");

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_nochexapi', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('textNCX_success'); 
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = "Nochex";

		$data['textNCX_edit'] = $this->language->get('textNCX_edit');
		$data['textNCX_enabled'] = $this->language->get('textNCX_enabled');
		$data['textNCX_disabled'] = $this->language->get('textNCX_disabled');
		$data['textNCX_all_zones'] = $this->language->get('text_all_zones');
		$data['textNCX_yes'] = $this->language->get('textNCX_yes');
		$data['textNCX_no'] = $this->language->get('textNCX_no');
		$data['textNCX_seller'] = $this->language->get('textNCX_seller');
		$data['textNCX_merchant'] = $this->language->get('textNCX_merchant');

		$data['entryNCX_email'] = $this->language->get('entryNCX_email');
		$data['entryNCX_account'] = $this->language->get('entryNCX_account');
		$data['entryNCX_merchant'] = $this->language->get('entryNCX_merchant');
		$data['entryNCX_merchantapi'] = $this->language->get('entryNCX_merchantapi');
		$data['entryNCX_template'] = $this->language->get('entryNCX_template');
		$data['entryNCX_test'] = $this->language->get('entryNCX_test');
		$data['entryNCX_total'] = $this->language->get('entryNCX_total');
		$data['entryNCX_order_status'] = $this->language->get('entryNCX_order_status');
		$data['entryNCX_geo_zone'] = $this->language->get('entryNCX_geo_zone');
		$data['entryNCX_status'] = $this->language->get('entryNCX_status');
		$data['entryNCX_sort_order'] = $this->language->get('entryNCX_sort_order');
		
		$data['entryNCX_hide'] = $this->language->get('entryNCX_hide');
		$data['entryNCX_callback'] = $this->language->get('entryNCX_callback');
		
		$data['entryNCX_xmlcollection'] = $this->language->get('entryNCX_xmlcollection');
		$data['entryNCX_debug'] = $this->language->get('entryNCX_debug');
		$data['entryNCX_postage'] = $this->language->get('entryNCX_postage');
		
		$data['helpNCX_test'] = $this->language->get('helpNCX_test');
		
		$data['helpNCX_billing'] = $this->language->get('helpNCX_billing');
		$data['helpNCX_debug'] = $this->language->get('helpNCX_debug');
		$data['helpNCX_postage'] = $this->language->get('helpNCX_postage');
		$data['helpNCX_xml'] = $this->language->get('helpNCX_xml');
		$data['helpNCX_callback'] = $this->language->get('helpNCX_callback');
		$data['helpNCX_merchantid'] = $this->language->get('helpNCX_merchantid');
		$data['helpNCX_merchant_api'] = $this->language->get('helpNCX_merchant_api');
		$data['helpNCX_total'] = $this->language->get('helpNCX_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['errorNCX_warning'] = $this->error['warning'];
		} else {
			$data['errorNCX_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['errorNCX_email'] = $this->error['email'];
		} else {
			$data['errorNCX_email'] = '';
		}

		if (isset($this->error['merchant'])) {
			$data['errorNCX_merchant'] = $this->error['merchant'];
		} else {
			$data['errorNCX_merchant'] = '';
		}

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('textNCX_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => "Payment",
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);
		$data['breadcrumbs'][] = array(
			'text' => "Nochex",
			'href' => $this->url->link('extension/nochexapi/payment/nochexapi', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/nochexapi/payment/nochexapi', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_nochexapi_email'])) {
			$data['payment_nochexapi_email'] = $this->request->post['payment_nochexapi_email'];
		} else {
			$data['payment_nochexapi_email'] = $this->config->get('payment_nochexapi_email');
		}

		if (isset($this->request->post['payment_nochexapi_account'])) { 
			$data['payment_nochexapi_account'] = $this->request->post['payment_nochexapi_account'];
		} else { 
			$data['payment_nochexapi_account'] = $this->config->get('payment_nochexapi_account');
		}

		if (isset($this->request->post['payment_nochexapi_merchant'])) { 
			$data['payment_nochexapi_merchant'] = $this->request->post['payment_nochexapi_merchant'];
		} else { 
			$data['payment_nochexapi_merchant'] = $this->config->get('payment_nochexapi_merchant');
		}
		
		if (isset($this->request->post['payment_nochexapi_merchantapi'])) { 
			$data['payment_nochexapi_merchantapi'] = $this->request->post['payment_nochexapi_merchantapi'];
		} else { 
			$data['payment_nochexapi_merchantapi'] = $this->config->get('payment_nochexapi_merchantapi');
		}


		if (isset($this->request->post['payment_nochexapi_template'])) { 
			$data['payment_nochexapi_template'] = $this->request->post['payment_nochexapi_template'];
		} else { 
			$data['payment_nochexapi_template'] = $this->config->get('payment_nochexapi_template');
		}

		if (isset($this->request->post['payment_nochexapi_test'])) { 
			$data['payment_nochexapi_test'] = $this->request->post['payment_nochexapi_test'];
		} else { 
			$data['payment_nochexapi_test'] = $this->config->get('payment_nochexapi_test');
		}
		
		if (isset($this->request->post['payment_nochexapi_callback'])) { 
			$data['payment_nochexapi_callback'] = $this->request->post['payment_nochexapi_callback'];
		} else {
			$data['payment_nochexapi_callback'] = $this->config->get('payment_nochexapi_callback'); 
		}
		
		if (isset($this->request->post['payment_nochexapi_xmlcollection'])) { 
			$data['payment_nochexapi_xmlcollection'] = $this->request->post['payment_nochexapi_xmlcollection'];
		} else { 
			$data['payment_nochexapi_xmlcollection'] = $this->config->get('payment_nochexapi_xmlcollection');
		}
		if (isset($this->request->post['payment_nochexapi_debug'])) { 
			$data['payment_nochexapi_debug'] = $this->request->post['payment_nochexapi_debug'];
		} else { 
			$data['payment_nochexapi_debug'] = $this->config->get('payment_nochexapi_debug');
		}
		
		if (isset($this->request->post['payment_nochexapi_postage'])) { 
			$data['payment_nochexapi_postage'] = $this->request->post['payment_nochexapi_postage'];
		} else { 
			$data['payment_nochexapi_postage'] = $this->config->get('payment_nochexapi_postage');
		}
		
		if (isset($this->request->post['payment_nochexapi_hide'])) { 
			$data['payment_nochexapi_hide'] = $this->request->post['payment_nochexapi_hide'];
		} else { 
			$data['payment_nochexapi_hide'] = $this->config->get('payment_nochexapi_hide');
		}
		
		if (isset($this->request->post['payment_nochexapi_total'])) { 
			$data['payment_nochexapi_total'] = $this->request->post['payment_nochexapi_total'];
		} else { 
			$data['payment_nochexapi_total'] = $this->config->get('payment_nochexapi_total');
		}

		if (isset($this->request->post['payment_nochexapi_order_status_id'])) {
			$data['payment_nochexapi_order_status_id'] = $this->request->post['payment_nochexapi_order_status_id']; 
		} else { 
			$data['payment_nochexapi_order_status_id'] = $this->config->get('payment_nochexapi_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_nochexapi_geo_zone_id'])) { 
			$data['payment_nochexapi_geo_zone_id'] = $this->request->post['payment_nochexapi_geo_zone_id'];
		} else { 
			$data['payment_nochexapi_geo_zone_id'] = $this->config->get('payment_nochexapi_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['payment_geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
 
		if (isset($this->request->post['payment_nochexapi_status'])) {
 
			$data['payment_nochexapi_status'] = $this->request->post['payment_nochexapi_status'];

		} else {
 
			$data['payment_nochexapi_status'] = $this->config->get('payment_nochexapi_status');
		}

		if (isset($this->request->post['payment_nochexapi_sort_order'])) { 
			$data['payment_nochexapi_sort_order'] = $this->request->post['payment_nochexapi_sort_order'];
		} else { 
			$data['payment_nochexapi_sort_order'] = $this->config->get('payment_nochexapi_sort_order');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/nochexapi/payment/nochexapi', $data)); 
	}

	protected function validate() {
	
		if (!$this->user->hasPermission('modify', 'extension/nochexapi/payment/nochexapi')) {
			$this->error['warning'] = $this->language->get('errorNCX_permission');
		}

		if (!$this->request->post['payment_nochexapi_merchant']) {
			$this->error['merchant'] = $this->language->get('errorNCX_merchant');
		}

		/*if (!$this->request->post['payment_nochexapi_merchantapi']) {
			$this->error['errorNCX_merchantapi'] = $this->language->get('errorNCX_merchantapi');
		}*/
		
		return !$this->error;
	}
}