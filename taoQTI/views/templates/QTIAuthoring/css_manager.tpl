<?if(count(get_data('cssFiles'))):?>
<div>
	<?=__('Attached style sheets:')?>
	<ul id="cssFiles">
	<?foreach(get_data('cssFiles') as $file):?>
		<li rel="<?=$file['href']?>">
			<span class="cssFile-title" title="<?=$file['title']?>"><a href="<?=$file['downloadUrl']?>"><?=$file['title']?></a></span>
			<a class="cssFile-delete cssFile-button" title="<?=__('delete')?>" href="#"><span class="ui-icon ui-icon-circle-close"></span></a>
			<a class="cssFile-download cssFile-button" title="<?=__('download')?>" href="<?=$file['downloadUrl']?>"><span class="ui-icon ui-icon-circle-arrow-s"></span></a>
		</li>
	<?endforeach;?>
	</ul>

	<hr size="1" width="100%">
</div>

<?endif;?>
<div class="main-container">
	<a id="a_cssFormToggleButton"><span id="cssFormToggleButton" class="ui-icon ui-icon-circle-plus"></span></a><span><?=__('Add a new stylesheet')?></span>
	<?=get_data('myForm')?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('a.cssFile-delete').click(function(){
			var $li = $(this).parent('li');
			if($li.length){
				myItem.deleteStyleSheet($li.attr('rel'));
			}
			return false;
		});
	});
</script>