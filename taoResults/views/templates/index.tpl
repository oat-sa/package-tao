<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?=BASE_WWW?>img/taoResults.png" /> <?=__('Results')?></h1>
		<p><?=__('All results of passed Tests with their referring Subject, Group and Item specific data, as well as the individual data collected during Test execution are stored and managed here.')?></p>
	</div>
</div>
<?php
Template::inc('footer.tpl', 'tao');
?>