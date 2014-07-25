<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default qti-authoring-form-container">
	<?=  get_data('choiceType')?> <?=__('Editor')?>
</div>
<div class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
    
	<div id="formContainer_groups_container" class="qti-authoring-form-container-column">
		<div id="formContainer_groups" class="formContainer_choices">
		<div class="choices-column-header"><?=__('Gaps')?></div>
		<?foreach(get_data('formGroups') as $gapSerial => $groupForm):?>
			<div id='<?=$gapSerial?>' class="formContainer_choice">
				<?=$groupForm?>
			</div>
		<?endforeach;?>
		</div>
		
		<div id="add_group_button" class="add_choice_button">
			<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"><?=__('Add choice')?></a>
			<?=__('quantity')?>
			<input id="add_group_number" type="text" size="1" maxLength="2" value="1"/>
		</div>
	</div>

	<div id="formContainer_choices_container" class="qti-authoring-form-container-column">
		<div id="formContainer_choices" class="formContainer_choices">
		<div class="choices-column-header"><?=__('Gap images')?></div>
		<?foreach(get_data('formChoices') as $choiceSerial => $choiceForm):?>
			<div id='<?=$choiceSerial?>' class="formContainer_choice">
				<?=$choiceForm?>
			</div>
		<?endforeach;?>
		</div>

		<div id="add_choice_button" class="add_choice_button">
			<a href="#"><img src="<?=ROOT_URL?>/tao/views/img/add.png"> <?=__('Add choice')?></a>
			<?=__('quantity')?>
			<input id="add_choice_number" type="text" size="1" maxLength="1" value="1"/>
		</div>
	</div>
	
	<div style="clear:both" />
</div>	

<script type="text/javascript">

$(document).ready(function(){
	$('a.form-choice-adder, #add_choice_button a').click(function(){
		var number = 1;
		var val = parseInt($("#add_choice_number").val());
		if(val){
			number = val;
		}
		
		//add hotspots to the current interaction:
		myInteraction.addChoice(number, $('#formContainer_choices'), 'formContainer_choice', 'gapImg');
		return false;
	});
	
	$('#add_group_button a').click(function(){
		var number = 1;
		var val = parseInt($("#add_group_number").val());
		if(val){
			number = val;
		}
		
		//add gapImgs to the current interaction:
        myInteraction.addChoice(number, $('#formContainer_groups'), 'formContainer_choice');
		return false;
	});
	
	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();
	
});
</script>
