<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default">
	<?=get_data('choiceType')?> <?=__('Editor')?>
</div>
<?  $formChoices = get_data('formChoices');
	$groupSerials = get_data('groupSerials'); ?>
<div class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
	<?foreach($groupSerials as $order => $setNumer):?>
	<div id="formContainer_choices_container_<?=$setNumer?>" class="formContainer_choices qti-authoring-form-container-column">
		<div class="choices-column-header"><?=__('Choice group').' '.intval($order+1)?></div>
		<div id="formContainer_choices_<?=$setNumer?>" class="qti-authoring-form-container">
		<?foreach($formChoices[$setNumer] as $choiceId => $choiceForm):?>
			<div id='<?=$choiceId?>' class='formContainer_choice'>
				<?=$choiceForm?>
			</div>
		<?endforeach;?>
		</div>

		<div id="add_choice_button_<?=$setNumer?>" class="add_choice_button">
			<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"> <?=__('Add choice to group').' '.intval($order+1)?></a>
			<?=__('quantity')?>
			<input id="add_choice_number_<?=$order?>" type="text" size="1" maxLength="1" value="1"/>
		</div>
	</div>
	
	<?endforeach;?>
	<div style="clear:both">
</div>	



<script type="text/javascript">
$(document).ready(function(){
	<?foreach($groupSerials as $order => $setNumer):?>
	$('#add_choice_button_<?=$setNumer?> a').click(function(){
		var number = 1;
		
		var val = parseInt($("#add_choice_number_<?=$order?>").val());
		if(val){
			number = val;
		}
		
		//add a choice to the current interaction:
		myInteraction.addChoice(number, $('#formContainer_choices_<?=$setNumer?>'), 'formContainer_choice', '<?=$setNumer?>');
		return false;
	});
	<?endforeach;?>
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();
	
});
</script>
