<?php
class ModelModuleProductStock extends Model {
    public function getStocks() {

        $query = $this->db->query('SELECT * FROM oc_product_stock WHERE stock_id IN (SELECT DISTINCT stock_id FROM oc_product_to_stock)');

        return $query->rows;
    }
}
