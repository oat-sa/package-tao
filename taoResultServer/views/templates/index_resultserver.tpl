<?php
use oat\tao\helpers\Template;

Template::inc('header.tpl');
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=BASE_WWW?>img/resultServer.png" />&nbsp;&nbsp;<?=__("Result Server")?></h1>
		<p><?=__("The Result Server Management section allows to add, edit and assign Delivery Result Server to Deliveries.")?><br />
		<?=__("A delivery server is required for every delivery, it defines where the results of that delivery will be uploaded.")?> </p>
	</div>
</div>
<?php
Template::inc('footer.tpl');
?>