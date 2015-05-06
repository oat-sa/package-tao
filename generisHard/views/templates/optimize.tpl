<?php use oat\tao\helpers\Template as _tpl;?>
<link rel="stylesheet" href="<?=_tpl::css('optimize.css', 'tao')?>" type="text/css" />

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