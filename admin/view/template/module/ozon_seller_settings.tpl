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
					style="max-width: 80px"
					id="discount_percent" 
					placeholder="0" 
					name="ozon_seller_margin">
		</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2">Дополнительная наценка по условию</label>
			<div class="col-sm-10 discount_step__box">
				<?php $i = 0; ?>
				<?php foreach($discount_steps as $step => $ratio) {?>
				<div class="discount_step__item">
					<div class="discount_step__min_border">
						<label for="step_key<?php echo $i;?>">Если цена менее (руб.)</label>
						<input type="number" step="10" value="<?php echo $step; ?>" class="form-control" name="step_key<?php echo $i;?>" id="step_key<?php echo $i;?>"></input>
					</div>
					<div class="discount_step__ratio">
						<label for="step_value<?php echo $i;?>">То коэффициент</label>
						<input type="number" step="0.1" value="<?php echo $ratio; ?>" class="form-control" name="step_value<?php echo $i;?>" id="step_value<?php echo $i;?>"></input>
					</div>
					<div class="discount_step__remove">
						<button type="button" class="btn btn-sm btn_remove_step" data-for="step_key<?php echo $i;?>">Убрать</button>
					</div>
				</div>
				<?php $i++;} ?>
				<h3>Добавить новый коэффициент</h3>
				<div class="discount_step__item">
					<div class="discount_step__min_border">
						<label for="step_key<?php echo $i;?>">Если цена менее (руб.)</label>
						<input type="number" step="10" value="" class="form-control" name="step_key_new" id="step_key_new"></input>
					</div>
					<div class="discount_step__ratio">
						<label for="step_value<?php echo $i;?>">То коэффициент</label>
						<input type="number" step="0.1" value="" class="form-control" name="step_value_new" id="step_value_new"></input>
					</div>
					<div class="discount_step__remove">
						<button type="submit" class="btn btn-sm" id="btn_add_step">Добавить</button>
					</div>
				</div>
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
				<p><?php echo $discount_steps_description; ?></p>
				<p>Генерируемый файл доступен по ссылке: <a href="<?php echo $feed_uri; ?>" target="_blank"><?php echo $feed_uri; ?></a></p>
			</div>
		</div>
		<div class="form-group">        
			  <div class="text-center">
				<button id="save_settings_btn" type="submit" class="btn btn-default btn-success">Сохранить настройки</button>
			  </div>
		</div>
	</form>
  
</div>

<style>
	.discount_step__box {
		display: flex;
		flex-direction: column;
		justify-content: start;
		align-items: start;
		gap: 20px;
	}
	.discount_step__item {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
		align-items: center;
		gap: 20px;
	}
	.discount_step__item > div {
		display: flex;
		flex-direction: row;
		justify-content: start;
		justify-content: space-between;
		align-items: center;
		gap: 10px;
	}
</style>

<script>
document.addEventListener("DOMContentLoaded", (event) => {
  	document.querySelectorAll('.btn_remove_step').forEach((btn) => 
	{	
		btn.addEventListener('click', async () => {
			btn.disabled = true;
			const stepKeyValue = document.querySelector('#' + btn.dataset.for).value;

			const res = await fetch('<?php echo html_entity_decode($remove_step_uri); ?>&key_to_delete=1000', {
				method: "POST"
			});
			
			window.location.reload();
						
		});
	});
});
</script>

<?php echo $footer ?>