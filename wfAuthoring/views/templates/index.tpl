<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=Template::img('taoProcess.png')?>" /> <?=__('Processes')?></h1>
		<p><?=__('The Processes module allows to create Processes.')?></p> 
	</div>
</div>
<?php Template::inc('footer.tpl'); ?>