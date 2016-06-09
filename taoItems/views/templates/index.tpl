<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=Template::img('taoItems.png')?>" /> <?=__('Items')?></h1>
		<p><?=__('The Items module enables the creation and design of items and exercises.')?> 
		</p> 
	</div>
</div>
<?php
Template::inc('footer.tpl');
?>