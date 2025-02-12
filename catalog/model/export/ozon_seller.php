<?php
class ModelExportOzonSeller extends Model {		
	public function getProducts($allowedStocksIds) {
		
		if(!$allowedStocksIds || strlen(trim($allowedStocksIds, ' \t,')) == 0){
			$allowedStocksIds = 0;
		}
		
		$sql = "SELECT p.sku, p.model,
						p.price, 
						SUM(IFNULL(IF(pts.stock_id IN (" . $allowedStocksIds  ."), pts.quantity, 0), 0)) as quantity,
						CONCAT('PSM-', p.product_id) as shopSku
						
				FROM oc_product p 
					JOIN oc_product_description pd ON (p.product_id = pd.product_id) 
					LEFT JOIN oc_product_to_stock pts ON (p.product_id = pts.product_id) 
				WHERE p.status = 1 
				GROUP BY p.product_id 
				ORDER BY p.sku";
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
}
?>