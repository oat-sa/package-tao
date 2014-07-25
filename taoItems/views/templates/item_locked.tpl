<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<link rel="stylesheet" type="text/css" href="<?= ROOT_URL ?>tao/views/css/layout.css" />
<div class="main-container">
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
<?=__('Item Locked')?>
</div>
    
	<span class="tileBox">
	    <span class="boxHeader">
		<?=__('This item is currently being edited and has been locked')?>
	    </span>
	    <span class="boxLogo">
		<img src="<?=TAOBASE_WWW?>/img/lock.png" />
	    </span>
	    <span class="boxContent">
		<table class=>
		    <tr><td class="fieldLabel"><?=__('Item Locked:')?></td><td class="fieldValue"><?=get_data('label')?></td></tr>
		    <tr><td class="fieldLabel"><?=__('Lock Owner:')?></td><td class="fieldValue"><A HREF="mailto:<?=get_data('ownerMail')?>"><?=get_data('ownerLogin')?></a></td></tr>
		    <tr><td class="fieldLabel"><?=__('Locking Date:')?></td><td class="fieldValue"><?=get_data('epoch')?></td></tr>
		    <tr />
		</table>
		
	    </span>
	    <span class="boxContent">
		<?php
		    if (get_data('isOwner')) {
		?>
		<br />
		<?=__('As the owner of this resource, you may release the lock')?>
		
		<span class="button" id="release">
		    <?=__('ReleaseLock')?>
		</span>
		<?php
		    } else {
		?>
		<br />
		<?=__('Please contact the owner of the resource to unlock it')?>
		<?php
		    }
		?>
	    </span>
	</span>
    
</div>
<script type="text/javascript">
    require(['jquery', 'helpers', 'lock'], function($, helpers, Lock){
        var lock = new Lock('<?=get_data('itemUri')?>');
        var successCallBack = function successCallBack() {
            helpers._load(
                helpers.getMainContainerSelector(),
                "<?=_url(null, null, null, array('uri' => get_data('itemUri')))?>"
            );
        };
        
        $("#release").click(function() {
            lock.release(successCallBack, successCallBack);
        });

        $('#tabs').tabs('enable', helpers.getTabIndexByName('items_preview'));
    });
</script>

<?php
if (!get_data('isDeprecated')) {
    Template::inc('footer.tpl');
}