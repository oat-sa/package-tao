<?php
use oat\tao\helpers\Template;
?>
<div class="main-container">
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<table>
			<tr>
				<th><?= __('Launch URL')?></th>
				<td><a href="<?= get_data('launchUrl')?>"><?= get_data('launchUrl')?></a></td>
			</tr>
			<tr>
				<th><?= __('Consumers')?></th>
				<td>
				<?php if (count(get_data('consumers')) > 0) :?>
    				<?php foreach (get_data('consumers') as $consumer) :?>
    				    <?= $consumer->getLabel()?><br />
    		        <?php endforeach;?>
    		    <?php else:?>
    		      <div><?= __('No LTI consumers defined')?></div>
    		    <?php endif;?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
Template::inc('footer.tpl', 'tao');
?>