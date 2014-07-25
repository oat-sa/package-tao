<link rel="stylesheet" type="text/css" href="<?=TAOBASE_WWW?>/css/lists.css" />
<div class="main-container">
	<div id="list-container">
		<?foreach(get_data('lists') as $i => $list):?>
				<div id='list-data_<?=$list['uri']?>' class="listbox">
					<fieldset>
						<legend><span><?=$list['label']?></span></legend>
						<div class="list-elements" id='list-elements_<?=$list['uri']?>'>
							<ol>
								<?foreach($list['elements'] as $level => $element):?>
									<li id="list-element_<?=$level?>">
										<span class="list-element" id="list-element_<?=$level?>_<?=$element['uri']?>" ><?=$element['label']?></span>
									</li>
								<?endforeach?>
							</ol>
						</div>
						<div class="list-controls">
						<?if($list['editable']):?>
							<a href="#" class="list-editor" id='list-editor_<?=$list['uri']?>'>
								<img src="<?=TAOBASE_WWW?>/img/pencil.png" class="icon" /><?=__('Edit')?>
							</a>
							|
							<a href="#" class="list-deletor" id='list-deletor_<?=$list['uri']?>'>
								<img src="<?=TAOBASE_WWW?>/img/delete.png" class="icon" /><?=__('Delete')?>
							</a>
						<?else:?>
							<?=__('Edit')?> | <?=__('Delete')?>

						<?endif?>
						</div>
					</fieldset>
				</div>
		<?endforeach?>

		<div style="margin-top:10px">
			<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
				<strong><?=__('Create a list')?></strong>
			</div>
			<div id="form-container" class="ui-widget-content ui-corner-bottom">
				<?=get_data('form')?>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
$(document).ready(function(){
	var saveUrl = "<?=_url('saveLists', 'Lists', 'tao')?>";
	var delListUrl = "<?=_url('removeList', 'Lists', 'tao')?>";
	var delEltUrl = "<?=_url('removeListElement', 'Lists', 'tao')?>";

	$(".list-editor").click(function(){
		uri = $(this).attr('id').replace('list-editor_', '');
		var listContainer = $("div[id='list-data_" + uri+"']");

		if(!listContainer.parent().is('form')){
			listContainer.wrap("<form class='listbox' />");
			listContainer.prepend("<input type='hidden' name='uri' value='"+uri+"' />");

			$("<input type='text' name='label' value='"+listContainer.find('legend span').text()+"'/>").prependTo(listContainer.find('div.list-elements')).keyup(function(){
				listContainer.find('legend span').text($(this).val());
			});

			if (listContainer.find('.list-element').length){
				listContainer.find('.list-element').replaceWith(function(){
					return "<input type='text' name='" + $(this).attr('id') + "' value='"+$(this).text()+"' />";
				});
			}

			elementList = listContainer.find('ol');
			elementList.addClass('sortable-list');
			elementList.find('li').addClass('ui-state-default');
			elementList.find('li').prepend('<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>');
			elementList.find('li').prepend('<span class="ui-icon ui-icon-arrowthick-2-n-s" ></span>');
			elementList.find('li').append('<span class="ui-icon ui-icon-circle-close list-element-deletor" style="cursor:pointer;" ></span>');

			elementList.sortable({
				axis: 'y',
				opacity: 0.6,
				placeholder: 'ui-state-error',
				tolerance: 'pointer',
				update: function(event, ui){
					var map = {};
					$.each($(this).sortable('toArray'), function(index, id){
						map[id] = 'list-element_' + (index + 1);
					});
					$(this).find('li').each(function(){
						id = $(this).attr('id');
						if(map[id]){
							$(this).attr('id', map[id]);
							newName = $(this).find('input').attr('name').replace(id, map[id]);
							$(this).find('input').attr('name', newName);
						}
					});
				}
			});

			elementSaver = $("<a href='#'><img src='<?=TAOBASE_WWW?>img/save.png' class='icon' /><?=__('Save')?></a>");
			elementSaver.click(function(){
				$.postJson(
					saveUrl,
					$(this).parents('form').serializeArray(),
					function(response){
						if(response.saved){
							helpers.createInfoMessage(__("list saved"));
							helpers._load(helpers.getMainContainerSelector(), "<?=_url('index', 'Lists', 'tao')?>");
						}
					}
				);
			});
			elementList.after(elementSaver);

			elementList.after('<br />');

			elementAdder = $("<a href='#'><img src='<?=TAOBASE_WWW?>img/add.png' class='icon' /><?=__('New element')?></a>");
			elementAdder.click(function(){
				level = $(this).parent().find('ol').children().length + 1;
				$(this).parent().find('ol').append(
					"<li id='list-element_"+level+"' class='ui-state-default'>" +
						"<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>" +
						"<span class='ui-icon ui-icon-grip-dotted-vertical' ></span>" +
						"<input type='text' name='list-element_"+level+"_' />" +
						"<span class='ui-icon ui-icon-circle-close list-element-deletor' ></span>" +
					"</li>");
			});
			elementList.after(elementAdder);
		}

		$(".list-element-deletor").click(function(){
			if(confirm(__("Please confirm you want to delete this list element."))){
				var element = $(this).parent();
				uri = element.find('input:text').attr('name').replace(/^list\-element\_([1-9]*)\_/, '');
				$.postJson(
					delEltUrl,
					{uri: uri},
					function(response){
						if(response.deleted){
							element.remove();
							helpers.createInfoMessage(__("element deleted"));
						}
					}
				);
			}
		});
	});

	$(".list-deletor").click(function(){
		if(confirm(__("Please confirm you want to delete this list. This operation is not reversible."))){
			var list = $(this).parents("div.listbox");
			uri = $(this).attr('id').replace('list-deletor_', '');
			$.postJson(
				delListUrl,
				{uri: uri},
				function(response){
					if(response.deleted){
						helpers.createInfoMessage(__("list deleted"));
						list.remove();
					}
				}
			);
		}
	});
});
</script>
