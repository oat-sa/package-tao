<?php
use oat\ontoBrowser\helpers\Display;
use oat\tao\helpers\Template as _tpl;
?>
<link rel="stylesheet" type="text/css" href="<?=_tpl::css('browse.css')?>" />
<div class="main-container">
	<div>
		<form name=open id="openform"><input type="text" name="uri" id="openuri" size="100"></input>
			<input type="submit" value="<?=__('open')?>">
		</form>

	</div>
	<h2><?=$res->getLabel()?> (<?=$res->getUri()?>)</h2>
	<h3><?=__('types')?></h3>
	<?foreach($types as $instance):?>
		<a href='<?=$instance->getUri()?>' class="browseLink"><?=strlen($instance->getLabel()) > 0 ? $instance->getLabel() : __('noname')?></a><br />
	<?endforeach;?>
	<? if (isset($subclasses)) :?>
	<div>
		<h3><?=__('subclass of')?></h3>
		<?foreach($subclassOf as $subclass):?>
			<a href='<?=$subclass->getUri()?>' class="browseLink"><?=strlen($subclass->getLabel()) > 0 ? $subclass->getLabel() : __('noname')?></a><br />
		<?endforeach;?>
		<h3><?=__('subclasses')?></h3>
		<?foreach($subclasses as $subclass):?>
			<a href='<?=$subclass->getUri()?>' class="browseLink"><?=strlen($subclass->getLabel()) > 0 ? $subclass->getLabel() : __('noname')?></a><br />
		<?endforeach;?>
		<h3><?=__('instances')?></h3>
		<?foreach($instances as $instance):?>
			<a href='<?=$instance->getUri()?>' class="browseLink"><?=strlen($instance->getLabel()) > 0 ? $instance->getLabel() : __('noname')?></a><br />
		<?endforeach;?>
	</div>
	<?php endif;?>

	<div>
		<h2><?=__('Triples with subject')?> <?=$res->getLabel()?></h2>
		<table>
			<tr><th><?=__('predicate')?></th><th><?=__('object')?></th><th class="small"><?=__('ModelId')?></th><th class="small"><?=__('lg')?></th></tr>
			<?foreach($triples as $triple):	?>
				<? if ($triple->lg == '' || $triple->lg == DEFAULT_LANG || $triple->lg ==get_data("userLg")) $class="inScope"; else $class="notInScope"; ?>
				<tr class="<?= $class ?>">
				    <td><a href="<?=$triple->predicate?>" class="browseLink"><?=Display::reverseConstantLookup($triple->predicate)?></a></td><td><?
					if (\common_Utils::isUri($triple->object)) {
						$obj = new \core_kernel_classes_Resource($triple->object);
						echo '<a href="'.$obj->getUri().'" class="browseLink">'.(strlen($obj->getLabel()) > 0 ? $obj->getLabel() : __('noname')).'</a>';
					} else {
						echo $triple->object;
					}
				?></td><td ><?=$triple->modelid?></td><td ><?=$triple->lg?></td></tr>
			<? endforeach;?>
		</table>
		<h2><?=__('Triples with object')?> <?=$res->getLabel()?></h2>
		<table>
			<tr><th><?=__('subject')?></th><th><?=__('predicate')?></th><th class="small"><?=__('ModelId')?></th><th class="small"><?=__('lg')?></th></tr>
			<?foreach($otriples as $triple):?>
				<? if ($triple->lg == '' || $triple->lg == DEFAULT_LANG || $triple->lg ==get_data("userLg")) $class="inScope"; else $class="notInScope"; ?>
				<tr class="<?= $class ?>">
				    <td><?
					if (\common_Utils::isUri($triple->subject)) {
						$obj = new \core_kernel_classes_Resource($triple->subject);
						echo '<a href="'.$obj->getUri().'" class="browseLink">'.(strlen($obj->getLabel()) > 0 ? $obj->getLabel() : __('noname')).'</a>';
					} else {
						echo $triple->subject;
					}
				?></td><td><?=Display::reverseConstantLookup($triple->predicate)?></td><td ><?=$triple->modelid?></td><td ><?=$triple->lg?></td></tr>
			<? endforeach;?>
		</table>
		<h2><?=__('Triples with predicate')?> <?=$res->getLabel()?></h2>
		<table>
			<tr><th><?=__('subject')?></th><th><?=__('object')?></th><th class="small"><?=__('ModelId')?></th><th class="small"><?=__('lg')?></th></tr>
			<?foreach($ptriples as $triple): ?>
				<? if ($triple->lg == '' || $triple->lg == DEFAULT_LANG || $triple->lg ==get_data("userLg")) $class="inScope"; else $class="notInScope"; ?>
				<tr class="<?= $class ?>">
				    <td><?
					if (\common_Utils::isUri($triple->subject)) {
						$obj = new \core_kernel_classes_Resource($triple->subject);
						echo '<a href="'.$obj->getUri().'" class="browseLink">'.(strlen($obj->getLabel()) > 0 ? $obj->getLabel() : __('noname')).'</a>';
					} else {
						echo $triple->object;
					}
				?></td><td><?=Display::reverseConstantLookup($triple->object)?></td><td><?=$triple->modelid?></td><td><?=$triple->lg?></td></tr>
			<? endforeach;?>
		</table>
	</div>

</div>
<?php _tpl::inc('footer.tpl', 'tao');?>