<?php 
class ControllerFeedOzonSeller extends Controller {
	protected $eol = "\n";
	
	public function index() {

		$stockName = 'proskit-market';		
		$this->load->model('export/ozon_seller');		
		$this->load->model('setting/setting');
		$settings = $this->model_setting_setting->getSetting('ozon_seller');
			
		$discount_percent = isset($settings['ozon_seller_margin']) ? (int)$settings['ozon_seller_margin'] : '0';
		$allowedStocksIds = isset($settings['ozon_seller_checked_stocks_ids']) ? $settings['ozon_seller_checked_stocks_ids'] : '0';
		
		$products = $this->model_export_ozon_seller->getProducts($allowedStocksIds);
		
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . $this->eol;
		$out .= '<yml_catalog date="' . date('Y-m-d H:i') . '">'. $this->eol;
		$out .= '<shop>'. $this->eol;
		
		$out .= '<offers>'. $this->eol;	
				
		foreach ($products as $product) {
			$offer_id = $product['shopSku'];
			$quantity = $product['quantity'];

			if($offer_id){
				$out .= '<offer id="' . $offer_id . '">'. $this->eol;	
				$price = $this->ApplyMinimumPriceMultiplierOptions($product['price'], $discount_percent);				
				$out .= '<price>' . $price . '</price>'. $this->eol;
				$out .= '<outlets>'. $this->eol;
				$out .= '<outlet instock="' . $quantity . '" warehouse_name="'.$stockName.'"></outlet>'. $this->eol;			
				$out .= '</outlets>'. $this->eol;			
				$out .= '</offer>'. $this->eol;		
			}			
		}
		
		$out .= '</offers>'. $this->eol;
		$out .= '</shop>'. $this->eol;
		$out .= '</yml_catalog>'. $this->eol;
		
		//$this->model_export_ozon_seller->saveLastRequestTime();
		
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($out);
	}
	
	function ApplyMinimumPriceMultiplierOptions($product_price, $discount_percent){
		
		$priceSteps = [
			'500' => '2.8', //Если менее 500 руб, то * цену на 2.8
			'1000' => '2.2' //Если менее 1000 руб, то * цену на 2.2	
		];

		$resultPrice = $product_price;
		
		if($product_price < 1000){
			foreach($priceSteps as $step_price => $ratio){
				if($product_price < $step_price){
					$resultPrice = $resultPrice * $ratio;
					break;
				}	
			}				
		}
		
		//Добавляем общую наценку, округляем до 10 руб.
		$resultPrice = (int)(ceil((float)$resultPrice * ((100.0 + (float)$discount_percent)/100.0) / 10) * 10);
		
		return $resultPrice;
	}
}