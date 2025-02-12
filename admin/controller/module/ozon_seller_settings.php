<?php
class ControllerModuleOzonSellerSettings extends Controller {
	public function index() {
		$this->document->setTitle('Настройки синхронизации цен с Ozon Seller');
		
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('ozon_seller');
		
		$data['action'] = $this->url->link('module/ozon_seller_settings', 'token=' . $this->session->data['token'] . $url, 'SSL');		
		$data['ozon_seller_margin'] = isset($settings['ozon_seller_margin']) ? $settings['ozon_seller_margin'] : '0';
		
		$checkedStockIds = isset($settings['ozon_seller_checked_stocks_ids']) ? explode(',', $settings['ozon_seller_checked_stocks_ids']) : [];
			
		$this->load->model('module/product_stock');		
		$allStocks = $this->model_module_product_stock->getStocks();
		$data['checked_stocks'] = [];
		foreach($allStocks as $stock){
			$stock['checked'] = in_array($stock['stock_id'], $checkedStockIds);
			$data['checked_stocks'][] = $stock;
		}
						
		if ($this->request->server['REQUEST_METHOD'] == 'POST'){
				
			$newMargin = (int)$this->request->post['ozon_seller_margin'];
			$this->model_setting_setting->editSettingValue('ozon_seller', 'ozon_seller_margin' , $newMargin);
			
			$checkedStocksIdsToSave = [];
			foreach($allStocks as $stock){
				if((bool)$this->request->post['stock_num' . $stock['stock_id']]){
					$checkedStocksIdsToSave[] =  $stock['stock_id'];
				}
			}
			$checkedStocksIdsToSaveString = implode(',', $checkedStocksIdsToSave);
			$this->model_setting_setting->editSettingValue('ozon_seller', 'ozon_seller_checked_stocks_ids' , $checkedStocksIdsToSaveString);
			
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/ozon_seller_settings', 'token=' . $this->session->data['token'], 'SSL'));			
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/ozon_seller_settings.tpl', $data));	
	}
}