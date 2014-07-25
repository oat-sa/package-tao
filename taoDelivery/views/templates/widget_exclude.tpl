<?php if (get_data('groupcount') > 0) : ?>
<div class="data-container">
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Test-takers')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
	   <div >
    	   <?php if (get_data('ttassigned') > 0) : ?>
    	       <?=__('Delivery is assigned to %s test-takers.', get_data('ttassigned')); ?>
	       <?php else: ?>
        	   <div class="feedback-info small">
            	   <span class="icon-info"></span>
        	       <?=__('Delivery is not assigned to any test-taker.'); ?>
    	       </div>
    	   <?php endif; ?>
	   </div>
	   <?php if (get_data('ttexcluded') > 0) : ?>
    	   <div class="feedback-info small">
    	       <span class="icon-info"></span>
    	       <?=__('%s test-taker(s) are excluded.', get_data('ttexcluded')); ?>
    	   </div>
	   <?php endif; ?>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<button id="exclude-btn" class="btn-info small" type="button"><?=__('Excluded test-takers')?></button>
		<!--  data-modal="#excluded-testtaker" -->
	</div>
</div>
<div id="modal-container" class="tao-scope">
    <div id="testtaker-form" class="modal">
    </div>	
</div>
<script type="text/javascript">
$('#exclude-btn').click(function() {
	jQuery('#testtaker-form').load('<?=_url('excludeTesttaker', 'Delivery')?>', {'uri' : '<?= get_data('assemblyUri')?>'}, function() {
		$('body').prepend($('#modal-container'));
		$('#testtaker-form').modal();
	});
})
</script>
<?php endif; ?>