<div id="item-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Available Items')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<span class="elt-info"><?=__('Select the items composing the test.')?></span>
		<div id="item-tree"></div>
		<div class="breaker"></div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<div id="item-order-container" class="data-container" >
	<div class="ui-widget ui-state-default ui-widget-header ui-corner-top container-title" >
		<?=__('Items sequence')?>
	</div>
	<div class="ui-widget ui-widget-content container-content">
		<div id="item-list">
			<span class="elt-info" <?php if (!count(get_data('itemSequence'))) echo ' style="display:none"' ?>><?=__('Drag and drop the items to order them')?></span>
			<ul id="item-sequence" class="sortable-list">
			<?foreach(get_data('itemSequence') as $index => $item):?>
				<li class="ui-state-default" id="item_<?=$item['uri']?>" >
					<span class='ui-icon ui-icon-arrowthick-2-n-s' ></span>
					<span class="ui-icon ui-icon-grip-dotted-vertical" ></span>
					<?=$index?>. <?=$item['label']?>
				</li>
			<?endforeach?>
			</ul>
		</div>
	</div>
	<div class="ui-widget ui-widget-content ui-state-default ui-corner-bottom" style="text-align:center; padding:4px;">
		<input id="saver-action-item-sequence" type="button" value="<?=__('Save')?>" />
	</div>
</div>
<input type='hidden' name='uri' value="<?=get_data('uri')?>" />

<script type="text/javascript">
var sequence = <?=get_data('relatedItems')?>;
var labels = <?=get_data('allItems')?>;

	$(function(){
		function buildItemList(id, items, labels){
			html = '';
			for (i in items) {
				itemId = items[i];
				html += "<li class='ui-state-default' id='" + itemId + "' >";
				html += "<span class='ui-icon ui-icon-arrowthick-2-n-s' /><span class='ui-icon ui-icon-grip-dotted-vertical' />";
				html += i + ". " + labels[itemId];
				html += "</li>";
			}
			$("#" + id).html(html);
		}

		require(['require', 'jquery', 'generis.tree.select'], function(req, $, GenerisTreeSelectClass) {
			saveurl = <?php echo json_encode(get_data('saveUrl'))?>

			new GenerisTreeSelectClass('#item-tree', root_url + 'tao/GenerisTree/getData',{
				actionId: 'item',
				saveUrl: saveurl,
				paginate:	10,
				saveCallback: function (data){
					if (buildItemList != undefined) {
						newSequence = {};
						sequence = [];
		                var uris = jQuery.parseJSON(data["instances"]);
		                for (attr in uris) {
		                    if ($.inArray(uris[attr], sequence) == -1 && attr != undefined) {
		                        newSequence[parseInt(attr.replace('instance_', ''))+1] = 'item_'+ uris[attr];
		                        sequence[parseInt(attr.replace('instance_', ''))+1] =  uris[attr];
		                    }
		                }
						buildItemList("item-sequence", newSequence, labels);
						if ($('#item-sequence li').length) $('#item-sequence').prev('.elt-info').show();
						else $('#item-sequence').prev('.elt-info').hide();
					}
				},
				checkedNodes : sequence,
				serverParameters: {
					openNodes: <?=json_encode(get_data('itemOpenNodes'))?>,
					rootNode: <?=json_encode(get_data('itemRootNode'))?>
				},
				callback: {
					checkPaginate: function(NODE, TREE_OBJ) {
						//Check the unchecked that must be checked... olè!
						this.check(sequence);
					}
				}
			});
		});

		$("#item-sequence").sortable({
			axis: 'y',
			opacity: 0.6,
			placeholder: 'ui-state-error',
			tolerance: 'pointer',
			update: function(event, ui){
				listItems = $(this).sortable('toArray');

				newSequence = {};
				sequence = [];
				for (i = 0; i < listItems.length; i++) {
					index = i+1;
					newSequence[index] = listItems[i];
					sequence[index] = listItems[i].replace('item_', '');
				}
				buildItemList('item-sequence', newSequence, labels);
			}
		});

		$("#item-sequence li").on('mousedown', function(){
			$(this).css('cursor', 'move');
		});
		$("#item-sequence li").on('mouseup', function(){
			$(this).css('cursor', 'pointer');
		});

		$("#saver-action-item-sequence").click(function(){
			toSend = {};
			for(index in sequence){
				toSend['instance_'+index] = sequence[index];
			}
			toSend.uri = $("input[name=uri]").val();
			toSend.classUri = $("input[name=classUri]").val();
			$.ajax({
				url: saveurl,
				type: "POST",
				data: toSend,
				dataType: 'json',
				success: function(response){
					if (response.saved) {
						helpers.createInfoMessage("<?=__('Sequence saved successfully')?>");
					}
				},
				complete: function(){
					helpers.loaded();
				}
			});
		});
	});
</script>