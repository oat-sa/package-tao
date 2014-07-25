<div id="formChoices_title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=  get_data('choiceType')?> <?=__('Editor')?>
</div>
<div id="formContainer_choices_title" class="ui-widget-content ui-corner-bottom qti-authoring-form-container">
	<div id="formContainer_choices" class="formContainer_choices">
	<?foreach(get_data('formChoices') as $choiceId => $choiceForm):?>
		<div id='<?=$choiceId?>' class='formContainer_choice'>
			<?=$choiceForm?>
		</div>
	<?endforeach;?>
	</div>

	<div id="add_choice_button" class="add_choice_button">
		<a href="#"><img src="<?=ROOT_URL?>tao/views/img/add.png"> <?=__('Add choice')?></a>
		<?=__('quantity')?>
		<input id="add_choice_number" type="text" size="1" maxLength="1" value="1"/>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('a.form-choice-adder, #add_choice_button a').click(function(){
		var number = 1;

		var val = parseInt($("#add_choice_number").val());
		if(val){
			number = val;
		}

		//add a choice to the current interaction:
		myInteraction.addChoice(number, $('#formContainer_choices'), 'formContainer_choice');
		return false;
	});

	//add adv. & delete button
	myInteraction.initToggleChoiceOptions();

});
</script>
