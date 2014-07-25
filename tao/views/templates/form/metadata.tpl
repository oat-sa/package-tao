<?if(get_data('metadata')):?>
<div id="meta-title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=__('Meta Data')?>
	<a href="#" id="meta-close"><span class="ui-icon ui-icon-circle-close" style="float: right;"></span></a>
</div>
<div id="meta-content" class="ui-widget-content">
	<table>
		<thead>
			<tr>
				<th class="first"><?=__('Date')?></th>
				<th><?=__('User')?></th>
				<th class="last"><?=__('Comment')?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach (get_data('comments') as $comment) :?>
			<tr>
				<td class="first"><?=$comment['date']?></td>
				<td><?=$comment['author']?></td>
				<td class="last">
					<span><?=$comment['text']?></span>
				</td>
			</tr>
			<?php endforeach;?>
			<tr id="meta-addition">
				<td class="first" colspan="2"/>
				<td class="last">
					<span id="comment-field"><?=get_data('comment')?></span>
					<a href="#" id="comment-editor" title="<?=__('Edit Comment')?>">
						<img src="<?=TAOBASE_WWW?>img/edit.png" alt="<?=__('Edit Comment')?>" />
					</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?endif?>