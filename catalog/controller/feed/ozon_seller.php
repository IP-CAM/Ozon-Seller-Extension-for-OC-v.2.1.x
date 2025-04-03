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
		$priceSteps = isset($settings['ozon_seller_discount_steps']) ? $settings['ozon_seller_discount_steps'] : [];

		$products = $this->model_export_ozon_seller->getProducts($allowedStocksIds);
		$directDiscounts = $this->model_export_ozon_seller->getDirectDiscounts();
		
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . $this->eol;
		$out .= '<yml_catalog date="' . date('Y-m-d H:i') . '">'. $this->eol;
		$out .= '<shop>'. $this->eol;
		
		$out .= '<offers>'. $this->eol;	
				
		foreach ($products as $product) {
			$offer_id = $product['shopSku'];
			$quantity = $product['quantity'];

			if($offer_id){
				$out .= '<offer id="' . $offer_id . '">'. $this->eol;

				$currentDiscount = (array_key_exists($product['shopSku'], $directDiscounts) && $directDiscounts[$product['shopSku']]) ? $directDiscounts[$product['shopSku']] : $discount_percent;

				$price = $this->ApplyDiscounts($product['price'], $currentDiscount, $priceSteps);				
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
	
	function ApplyDiscounts($product_price, $discount_percent, $priceSteps){
		
		//Пример priceSteps
		//$priceSteps = [
		//	'500' => '2.8', 
		//	'1000' => '2.2' 
		//];
		
		$resultPrice = (int)$product_price;
		
		if($priceSteps){
			$maxStep = (int)max(array_keys($priceSteps));

			if($product_price < $maxStep){
				foreach($priceSteps as $startValueInRub => $ratio){
					if($product_price < (int)$startValueInRub){
						$resultPrice = $resultPrice * (float)$ratio;
						break;
					}	
				}				
			}
		}
		
		//Добавляем общую наценку, округляем до 10 руб.
		$resultPrice = (int)(ceil((float)$resultPrice * ((100.0 + (float)$discount_percent)/100.0) / 10) * 10);
		
		$resultPrice = max($resultPrice, 0);
		
		return $resultPrice;
	}
	
	public function single_product_hint(){
		if ($this->request->server['REQUEST_METHOD'] == 'POST'){	
			$this->load->model('setting/setting');
			$settings = $this->model_setting_setting->getSetting('ozon_seller');
			$priceSteps = isset($settings['ozon_seller_discount_steps']) ? $settings['ozon_seller_discount_steps'] : [];
			$regularDiscount = isset($settings['ozon_seller_margin']) ? (int)$settings['ozon_seller_margin'] : '0';
			
			$inputJSON = file_get_contents('php://input');
			$input = json_decode($inputJSON, TRUE); //convert JSON into array
			
			$discount = $input['ozon_seller_discount'] == 'regular_discount' ?  $regularDiscount : (int)$input['ozon_seller_discount'];
			$price = (int)$input['product_price'];
			
			$hintValue = $this->ApplyDiscounts($price, $discount, $priceSteps);
	
			header('Content-Type: application/json');
			echo json_decode($hintValue);
		}
	}
}