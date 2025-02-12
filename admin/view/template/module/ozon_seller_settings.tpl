<?php echo $header; ?><?php echo $column_left; ?>

<div id="content" class="ozon_seller_settings_admin_container">
	<h1>Настройка синхронизации цен с Ozon Seller</h1>
	<form class="form-horizontal" action="<?php echo $action; ?>" id="updater_form" method="post">
		<div class="form-group">
		<label class="control-label col-sm-2" for="discount_percent">Наценка, %</label>
		<div class="col-sm-10">
			<input value="<?php echo $ozon_seller_margin; ?>"
					type="number" 
					min="-100" 
					max="100" 
					class="form-control" 
					id="discount_percent" 
					placeholder="0" 
					name="ozon_seller_margin">
		</div>
		</div>

	  <div class="form-group">
	  <label class="control-label col-sm-2">Остатки складов</label>
	  <div class="col-sm-10">
	  <fieldset>
	  <?php foreach($checked_stocks as $stock) { ?>
		<input type="checkbox" <?php echo $stock['checked'] ? "checked" : ""; ?> name="stock_num<?php echo $stock['stock_id']; ?>" value="<?php echo $stock['name']; ?>"><span><?php echo $stock['name']; ?></span><br> 
	  <?php }?>
	  </fieldset>
	  </div>
	  </div>
	  		<div class="form-group">
				<div class="text-center">
				<p>Наценка выгрузки в Ozon Seller, расчитывается от цены товара с сайта</p>
				<p>Если товар менее 500 руб. то применяется коэф. x2.8, от 500 до 1000 руб., то коэф. x2.2</p>
				<p>Генерируемый файл доступен по ссылке: <a href="https://proskit-market.ru/index.php?route=feed/ozon_seller" target="_blank">https://proskit-market.ru/index.php?route=feed/ozon_seller</a></p>
			</div>
	  </div>
		<div class="form-group">        
		  <div class="text-center">
			<button id="save_settings_btn" type="submit" class="btn btn-default btn-success">Сохранить настройки</button>
		  </div>
		</div>
	</form>
  
</div>

<?php echo $footer ?>