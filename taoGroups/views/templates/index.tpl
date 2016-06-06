<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div class="ext-home-container ui-state-highlight">
		<h1><img src="<?= Template::img('taoGroups.png')?>" /> <?=__('Groups')?></h1>
		<p><?=__('The Groups module allows to group Test takers according to global features and classifications.')?><br />
		
	</div>
</div>
<?php
Template::inc('footer.tpl', 'tao')
?>
