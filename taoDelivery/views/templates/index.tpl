<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=BASE_WWW?>img/taoDelivery.png" />&nbsp;&nbsp; <?=__('Deliveries')?></h1>
		
		<p><?=__('The Deliveries module allows users to define tests deliveries parameters.')?></p>
		
	
		
	</div>
</div>
<?php
Template::inc('footer.tpl');
?>