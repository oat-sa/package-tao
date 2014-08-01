<?$sectionName = get_data("sectionName");//must be either activity or process?>

<div id="<?=$sectionName?>-property-form">
	<?if(!get_data("saved")):?>
		<?=get_data("myForm")?>
		<input type="button" name="submit-<?=$sectionName?>-property" id="submit-<?=$sectionName?>-property" value="<?=__("save")?>"/>
		<input type="button" id="reload-<?=$sectionName?>-property" value="<?=__("reload")?>"/>
		<input type="button" id="cancel-<?=$sectionName?>-property" value="<?=__("cancel")?>"/>
		<script type="text/javascript">
			function switchACLmode(){

				var restrictedUserElt = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_ACTIVITIES_RESTRICTED_USER)?>\']').parent();
				var restrictedRoleElt = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_ACTIVITIES_RESTRICTED_ROLE)?>\']').parent();
				var mode = $('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_ACTIVITIES_ACL_MODE)?>\']').val();

				if(mode == '<?=tao_helpers_Uri::encode(INSTANCE_ACL_USER)?>'){//mode "user"
					restrictedUserElt.show();//restricted user prop
					//empty the value and hide:
					restrictedRoleElt.hide();
				}
				else if(mode == '<?=tao_helpers_Uri::encode(INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED)?>'){//mode "restricted user role inherited"
					var activityUri = $("#activityPropertyEditor :input[name='activityUri']").val();
					if(activityUri){
						$.postJson( "<?=_url('getActivityInheritableRoles', 'ProcessAuthoring')?>",
							{
								activityUri: activityUri,
								classUri:  $("#activityPropertyEditor :input[name='classUri']").val()
							},
							function (response){
								if(response.roles){
									var roles = response.roles;
									restrictedRoleElt.find('option').each(function(){
										if(this.val != ''){
											var found = false;
											for(var roleUri in roles){
												if(roleUri == this.value){
													found = true; break;
												}
											}
											if(!found){
												this.style.display = 'none';
											}
										}
									});

									restrictedRoleElt.show();
									restrictedUserElt.hide();
								}
							}
						);
					}
					else{
						restrictedRoleElt.show();
						restrictedUserElt.hide();
					}
				}
				else if(mode == ''){
					restrictedRoleElt.hide();
					restrictedUserElt.hide();
				}
				else{
					restrictedRoleElt.find('option').css({'display':''});
					restrictedRoleElt.show();
					restrictedUserElt.hide();
				}
			}

			$(document).ready(function(){

				<?if($sectionName=="activity"):?>
					switchACLmode();
					$('select[id=\'<?=tao_helpers_Uri::encode(PROPERTY_ACTIVITIES_ACL_MODE)?>\']').change(switchACLmode);
				<?endif;?>

				//edit the id of the tag of uri:
				$("#<?=$sectionName?>-property-form input[id=uri]").attr("name","<?=$sectionName?>Uri");

				//change to submit event interception would be "cleaner" than adding a button
				$("#submit-<?=$sectionName?>-property").click(function(){

					$.ajax({
						url: authoringControllerPath+'edit<?=ucfirst($sectionName)?>Property',
						type: "POST",
						data: $("#<?=$sectionName?>-property-form :input").serialize(),
						dataType: 'html',
						success: function(response){
							$("#<?=$sectionName?>-property-form").html(response);

							<?if($sectionName=="process"):?>
							processProperty();
							<?endif;?>

							<?if($sectionName=="activity"):?>
							refreshActivityTree();
							<?endif;?>

						}
					});
				});

				$("#reload-<?=$sectionName?>-property").click(function(){
					<?if($sectionName=="process"):?>
						processProperty();
					<?endif;?>

					<?if($sectionName=="activity"):?>
						//reselect the node
						ActivityTreeClass.selectTreeNode($("#<?=$sectionName?>-property-form input[id=uri]").val());
					<?endif;?>
				});

				$("#cancel-<?=$sectionName?>-property").click(function(){
					$("#<?=$sectionName?>-property-form").html('');
				});

			});
		</script>
	<?else:?>

		<?if(get_data('newLabel')):?>
			<script type="text/javascript">
				//TODO: check if the label has been updated then replace it in the activity diagram object anyway:

				//for the time being, simply reload the whole diagram (not resource efficient):
				ActivityDiagramClass.reloadDiagram();
			</script>
		<?endif;?>

		<p><?=ucfirst($sectionName)?> property saved</p>

	<?endif;?>
</div>


