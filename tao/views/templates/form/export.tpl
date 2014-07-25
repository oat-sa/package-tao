<?php
use oat\tao\helpers\Template;
?>
<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=get_data('formTitle')?>
</div>
<?if (has_data('myForm')):?>
	<div class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
<?php endif;?>
<br />
<?if (has_data('download')):?>
	<iframe src="<?=get_data('download');?>" style="height: 0px;min-height: 0px"></iframe>
<?php endif;?>

<script type="text/javascript">
require(['jquery'], function($) {
    //by changing the format, the form is sent
    $(":radio[name='exportHandler']").change(function(){
            var form = $(this).parents('form');
            $(":input[name='"+form.attr('name')+"_sent']").remove();
            form.find('.form-submiter').click();
    });
});
</script>

<?php
Template::inc('footer.tpl', 'tao')
?>