<?php
class ControllerModuleOzonSellerSettings extends Controller {
	public function index() {
		$this->document->setTitle('Настройки синхронизации цен с Ozon Seller');
		
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('ozon_seller');
		
		$data['action'] = $this->url->link('module/ozon_seller_settings', 'token=' . $this->session->data['token'] . $url, 'SSL');	
		$data['remove_step_uri'] = $this->url->link('module/ozon_seller_settings/remove_step', 'token=' . $this->session->data['token'] . $url, 'SSL');	
		$data['feed_uri'] = "/index.php?route=feed/ozon_seller";
		
		$data['ozon_seller_margin'] = isset($settings['ozon_seller_margin']) ? $settings['ozon_seller_margin'] : '0';
		
		//Загружаем границы дополнительных наценок на дешевые товары
		$discount_steps = isset($settings['ozon_seller_discount_steps']) ? $settings['ozon_seller_discount_steps'] : [];	
		if($discount_steps){
			ksort($discount_steps);
		}

		if($discount_steps){
			$discount_steps_description = "Если цена товара менее чем: <br>";
			foreach($discount_steps as $startValueInRub => $ratio){
				$discount_steps_description .= ("${startValueInRub} руб. дополнительный коэффициент x${ratio}<br>");
			}
			$data['discount_steps_description'] = trim($discount_steps_description, '<br>');
		} else {
			$data['discount_steps_description'] = "Дополнительных наценок на дешевые товары не добавлено";
		}
		$data['discount_steps'] = $discount_steps;
		
		//Загружаем список отмеченых складов
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
			
			//Сохраняем отмеченные склады
			$checkedStocksIdsToSave = [];
			foreach($allStocks as $stock){
				if((bool)$this->request->post['stock_num' . $stock['stock_id']]){
					$checkedStocksIdsToSave[] =  $stock['stock_id'];
				}
			}			
			$checkedStocksIdsToSaveString = implode(',', $checkedStocksIdsToSave);
			$this->model_setting_setting->editSettingValue('ozon_seller', 'ozon_seller_checked_stocks_ids' , $checkedStocksIdsToSaveString);

			//Сохраняем границы наценок
			$hasNewStepKey = isset($this->request->post['step_key_new']) && $this->request->post['step_key_new'];
			$hasNewStepValue = isset($this->request->post['step_value_new']) && $this->request->post['step_value_new'];
			if($hasNewStepKey && $hasNewStepValue)
			{
				$newStepsData = $data['discount_steps'];
				$newStepsData[$this->request->post['step_key_new']] = $this->request->post['step_value_new'];
				ksort($newStepsData);
				$this->model_setting_setting->editSettingValue('ozon_seller', 'ozon_seller_discount_steps' , (array)$newStepsData);
			}

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('module/ozon_seller_settings', 'token=' . $this->session->data['token'], 'SSL'));			
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/ozon_seller_settings.tpl', $data));	
	}

	public function remove_step(){
		if ($this->request->server['REQUEST_METHOD'] !== 'POST'){
			return;
		}

		parse_str(html_entity_decode($this->request->server['QUERY_STRING']), $queryArray);
		$key_to_delete = (string)$queryArray['key_to_delete'];

		if($key_to_delete && (int)$key_to_delete > 0){
			$this->load->model('setting/setting');
			$settings = $this->model_setting_setting->getSetting('ozon_seller');
			$discount_steps = isset($settings['ozon_seller_discount_steps']) ? $settings['ozon_seller_discount_steps'] : [];

			if($discount_steps && array_key_exists($key_to_delete, $discount_steps)){
				unset($discount_steps[$key_to_delete]);
				ksort($discount_steps);
				$this->model_setting_setting->editSettingValue('ozon_seller', 'ozon_seller_discount_steps' , (array)$discount_steps);
			}
		}	
	}
}