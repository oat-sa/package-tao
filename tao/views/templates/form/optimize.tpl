<script type="text/javascript" src="<?=ROOT_URL?>tao/views/js/Switcher.js"></script>
<link rel="stylesheet" href="<?=ROOT_URL?>tao/views/css/optimize.css" type="text/css" />

<div id="compilation-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=__("TAO optimizer")?>
</div>
<div id="compilation-container" class="ui-widget-content ui-corner-bottom">
        
        <div id="compilation-compile-info" class="ext-home-container ui-state-highlight">
        		<?= __("Classes and their associated data can be stored in two different modes.")?>
        		<ul>
        				<li>
        						<strong><?=__("Design Mode:")?></strong> <?=__("suitable for data modeling (default mode).")?>
        				</li>
        				<li>
        						<strong><?=__("Production Mode:")?></strong> <?=__("recommended for maximum performance e.g. once your data model is stable.")?>
        				</li>
        		</ul>
        		<div id="compilation-compile-warning">
        			<strong><?=__("Warning:")?></strong> <?=__("make sure to back up your data before changing modes.")?>
        		</div>
        </div>
        
        <div id="compilation-grid-container">
                <div id="compilation-table-container">
                        <table id="compilation-grid" />
                </div>
                <div id="compilation-recompile-button-container">
                		<input type="button" value="<?=__("Switch to Production Mode")?>" id="compileButton"/>
                        <input type="button" value="<?=__("Switch to Design Mode")?>" id="decompileButton"/>
                </div>
        </div>
        
        <div id="compilation-grid-results" class="ext-home-container ui-state-highlight"/>
</div>

<script type="text/javascript">
        $(function(){
             var options = {
                     onStart:function(){
                             $('#compilation-grid-container').show();
                     },
                     onStartEmpty:function(){
                             $('#compilation-grid-results').show().html(__('There are no classes available for optimization.'));
                     },
                     onStartDecompile:function(){
                             $('#compilation-grid-container').show();
                     },
                     beforeComplete: function(){
                             $('#compilation-grid-results').show().html(__('Rebuilding indexes, it may take a while.'));
                     },
                     onComplete:function(switcher, success){
                             if(success){
                                     $('#compilation-grid-results').show().html(__('Switch to Production Mode completed.'));
                             }else{
                                      $('#compilation-grid-results').show().html(__('Cannot successfully build the optimized table indexes'));
                             }
                             
                     },
                     onCompleteDecompile:function(){
                                $('#compilation-grid-results').show().html(__('Switch to Design Mode completed'));
                                $('#compileButton').show();
                                $('#decompileButton').show();
                     }
             }
             
             var mySwitcher = new switcherClass('compilation-grid', options);
             mySwitcher.init();
             
             $('#compileButton').click(function(){
             			if(confirm(__('All classes in Design Mode will switch to Production Mode. Please confirm.'))){
                        		mySwitcher.startCompilation();
                        }
             });
             
             $('#decompileButton').click(function(){
                        if(confirm(__('All classes in Production Mode will switch to Design Mode. Please confirm.'))){
                                mySwitcher.startDecompilation();
                                $('#compilation-grid-results').hide();
                        }
             });
        });
</script>