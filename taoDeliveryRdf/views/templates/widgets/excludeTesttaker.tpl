<section>
	<header>
		<h1><?=__('Test-takers')?></h1>
	</header>
	<div>
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
	<footer>
        <?php if (get_data('ttassigned') > 0 || get_data('ttexcluded') > 0) : ?>
            <button id="exclude-btn" class="btn-info small" type="button" data-delivery="<?= get_data('assemblyUri')?>"><?=__('Excluded test-takers')?></button>
        <?php endif;?>
	</footer>
</section>
<div id="modal-container" class="tao-scope">
    <div id="testtaker-form" class="modal"></div>	
</div>
