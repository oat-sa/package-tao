<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= Template::css('report.css','tao') ?>" media="screen"/>
<script type="text/javascript">
	$(function() {
		require(['jquery', 'tao/report'], function($, report){
		
			// Fold action (show detailed report).
			$('#fold > input[type="checkbox"]').click(function() {
				report.fold();
			});
			
			// Continue button
			$('#import-continue').on('click', function() {
				window.location.reload();
			});
			
		});
	});
	
</script>
<div class="main-container">
	<div class="ui-widget-header ui-corner-top ui-state-default">
		<?php if (has_data('title')): ?>
			<?php echo $title; ?>
		<?php else: ?>
			<?php echo __('Import report'); ?>
		<?php endif; ?>
	</div>
	<div class="ui-widget-content ui-corner-bottom report">
		<? if (get_data('report')->hasChildren() === true): ?>
		<label class="tao-scope" id="fold">
			<span><?= __("Show detailed report"); ?></span>
			<input type="checkbox"/>
		</label>
		<?php endif; ?>
		<?php echo tao_helpers_report_Rendering::render(get_data('report')); ?>
	</div>
</div>