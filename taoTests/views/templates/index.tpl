<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=Template::img('taoTests.png')?>" /> <?=__('Tests')?></h1>
		<p><?=__('The Tests module allows to combine a selection of Items to a Test.')?> <br />
		<?=__('The Test mode (sequencing, scoring, cumulating, etc.) and layout are also configured here.')?></p> 
	</div>
</div>

<?php
Template::inc('footer.tpl', 'tao');
?>