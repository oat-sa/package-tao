/**
 * UiForm class enable you to manage form elements, initialize form component and bind common events
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 * @require uiBootstrap [uiBootstrap.js]
 * @require [helpers.js]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */

define(['module', 'jquery', 'i18n', 'helpers', 'context', 'generis.actions', 'jwysiwyg' ], function(module, $, __, helpers, context, generisActions) {
    
    function getUrl(action){
        var conf = module.config();
        return context.root_url + conf.extension + '/' + conf.module + '/' + action;
    }
    
	var UiForm = {
		init: function() {
                    var self = this;
                    this.counter = 0;
                    this.initFormPattern = new RegExp(['search', 'authoring', 'Import', 'Export', 'IO', 'preview'].join('|'));
                    this.initGenerisFormPattern =  new RegExp(['add', 'edit', 'mode'].join('|'), 'i');
                    this.initTranslationFormPattern = /translate/;
                    this.initNav();

                    $("body").ajaxComplete(function(event, request, settings) {
                            var testedUrl;
                            //initialize regarding the requested action
                            //async request waiting for html or not defined
                            if (settings.dataType === 'html' || !settings.dataType) {
                                    if (settings.url.indexOf('?') !== -1 ){
                                            testedUrl = settings.url.substr(0, settings.url.indexOf('?'));
                                    } else {
                                            testedUrl = settings.url;
                                    }

                                    self.initRendering();
                                    self.initElements();
                                    if (self.initGenerisFormPattern.test(testedUrl)) {
                                            self.initOntoForms();
                                    }
                                    if(self.initTranslationFormPattern.test(testedUrl)) {
                                            self.initTranslationForm();
                                    }
                            }
                    });
                    this.initRendering();
		},

		/**
		 * init form naviguation (submit by ajax)
		 */
		initNav: function() {
                    var self = this;
                    $("form").live('submit', function() {
                            return self.submitForm($(this));
                    });
		},

		/**
		 * make some adjustment on the forms
		 */
		initRendering: function() {
                    $("span.form_desc").each(function() {
                            var myHeight = parseInt($(this).height());
                            var parentHeight = parseInt($(this).parent().height());
                            if (myHeight > parentHeight) {
                                    $(this).parent().height(myHeight +'px');
                            }
                    });
		},

		initElements: function(){
			//save form button
			var that = this;
			$(".form-submiter").off('click').on('click', function() {
				var myForm = $(this).parents("form");
				if (that.submitForm(myForm)) {
					myForm.submit();
				}
				return false;
			});

			//revert form button
			$(".form-refresher").off('click').on('click', function() {
				var myForm = $(this).parents('form');
				$(":input[name='"+myForm.attr('name')+"_sent']").remove();
				
				if (that.submitForm(myForm)) {
					myForm.submit();
				}
				return false;
			});

			//translate button
			$(".form-translator").off('click').on('click', function() {
                            var url;
                            if ($("#uri") && $("#classUri")){
                                helpers.getMainContainer().load(getUrl('translateInstance'), {'uri': $("#uri").val(), 'classUri': $("#classUri").val()});
                            }
                            return false;
			});

			//map the wysiwyg editor to the html-area fields
			$('.html-area').each(function() {
				if ($(this).css('display') !== 'none') {
					$(this).wysiwyg({'css' : context.taobase_www + 'css/layout.css'});
				}
			});

			$('.box-checker').off('click').on('click', function() {
				var checker  = $(this);
				var regexpId = new RegExp('^'+checker.prop('id').replace('_checker', ''), 'i');

				if (checker.hasClass('box-checker-uncheck')) {
					$(":checkbox").each(function() {
						if (regexpId.test(this.id)) {
							this.checked = false;
						}
					});
					checker.removeClass('box-checker-uncheck');
					checker.text(__('Check all'));
				} else {
					$(":checkbox").each(function() {
						if (regexpId.test(this.id)) {
							this.checked = true;
						}
					});
					checker.addClass('box-checker-uncheck');
					checker.text(__('Uncheck all'));
				}

				return false;
			});
		},

		/**
		 * init special forms controls
		 */
		initOntoForms: function (){
			//open the authoring tool on the authoringOpener button
			$('input.authoringOpener').click(function() {
				var url = getUrl('authoring');
                                var tabName = module.config().module.toLowerCase() + '_authoring';
				var index = helpers.getTabIndexByName(tabName);
				if (index > -1) {
					if ($("#uri") && $("#classUri")) {
						$('#tabs').tabs('url', index, url + '?uri=' + $("#uri").val() +'&classUri=' + $("#classUri").val());
						$('#tabs').tabs('enable', index);
						$('#tabs').tabs('select', index);
					}
				}
			});

			$('input.editVersionedFile').each(function() {
				var infoUrl = context.root_url + 'tao/File/getPropertyFileInfo';
				var data = {
					'uri':$("#uri").val(),
					'propertyUri':$(this).siblings('label.form_desc').prop('for')
				};
				var $_this = $(this);
				$.ajax({
					 type: "GET",
					 url: infoUrl,
					 data: data,
					 dataType: 'json',
					 success: function(r){
						$_this.after('<span>'+r.name+'</span>');
					 }
				});
			});

			$('input.editVersionedFile').click(function() {
				var data = {
                                    'uri' :         $("#uri").val(),
                                    'propertyUri' : $(this).siblings('label.form_desc').prop('for')
				};

				helpers.getMainContainer().load(getUrl('editVersionedFile'), data);
				return false;
			});

			/**
			 * remove a form group, ie. a property
			 */
			function removeGroup() {
                            if (confirm(__('Please confirm property deletion!'))) {
                                var groupNode = $(this).parents(".form-group").get(0);
                                if (groupNode) {
                                    $(groupNode).remove();
                                }
                            }
			}

			//property form group controls
			$('.form-group').each(function() {
				var formGroup = $(this);
				if (/property\_[0-9]+$/.test(formGroup.prop('id'))) {
					var child = formGroup.children("div:first");

					var toggelerClass = 'ui-icon-circle-triangle-s';
					if (!formGroup.hasClass('form-group-opened')) {
						child.hide();
						toggelerClass = 'ui-icon-circle-triangle-e';
					}

					//toggle controls: plus/minus icon
					var toggeler = $("<span class='form-group-control ui-icon' title='expand' style='right:48px;'></span>");
					toggeler.addClass(toggelerClass);
					toggeler.click(function(){
						var control = $(this);
						if (child.css('display') === 'none') {
							child.show('slow');
							control.removeClass('ui-icon-circle-triangle-e');
							control.addClass('ui-icon-circle-triangle-s');
							control.prop('title', 'hide property');
						} else {
							child.hide('slow');
							control.removeClass('ui-icon-circle-triangle-s');
							control.addClass('ui-icon-circle-triangle-e');
							control.prop('title', 'show property');
						}
					});
					formGroup.prepend(toggeler);

					//delete control
					if (/^property\_[0-9]+/.test(formGroup.prop('id'))) {
						var deleter = $("<span class='form-group-control ui-icon ui-icon-circle-close' title='Delete' style='right:24px;'></span>");
						deleter.off('click').on('click', removeGroup);
						formGroup.prepend(deleter);
					}
				}
			});

			//property delete button
			 $(".property-deleter").off('click').on('click', removeGroup);

			 //property add button
			 $(".property-adder").off('click').on('click', function() {
                            generisActions.addProperty(null,  $("#classUri").val(), getUrl('addClassProperty'));
			 });

			 $(".property-mode").off('click').on('click', function() {
				 var mode = 'simple';
				 if ($(this).hasClass('property-mode-advanced')) {
					mode = 'advanced';
				}
				var url = $(this).parents('form').prop('action');

				helpers.getMainContainer().load(url, {
					'property_mode': mode,
					'uri': $("#uri").val(),
					'classUri': $("#classUri").val()
				});

				return false;
			 });

			 /**
				* display or not the list regarding the property type
				*/
			 function showPropertyList() {
				var elt = $(this).parent("div").next("div");
				if (/list$/.test($(this).val())) {
					if (elt.css('display') === 'none') {
						elt.show();
						elt.find('select').removeAttr('disabled');
					}
				} else if(elt.css('display') !== 'none') {
					elt.css('display', 'none');
					elt.find('select').prop('disabled', "disabled");
				}
			 }

			 /**
				* by selecting a list, the values are displayed or the list editor opens
				*/
			 function showPropertyListValues() {
				if ($(this).val() === 'new') {
					//Open the list editor: a tree in a dialog popup
					var rangeId = $(this).prop('id');
					var dialogId = rangeId.replace('_range', '_dialog');
					var treeId = rangeId.replace('_range', '_tree');
					var closerId = rangeId.replace('_range', '_closer');

					//dialog content to embed the list tree
					elt = $(this).parent("div");
					elt.append("<div id='"+ dialogId +"' style='display:none;' > " +
									"<span class='ui-state-highlight' style='margin:15px;'>" + __('Right click the tree to manage your lists') + "</span><br /><br />" +
									"<div id='"+treeId+"' ></div> " +
									"<div style='text-align:center;margin-top:30px;'> " +
										"<a id='"+closerId+"' class='ui-state-default ui-corner-all' href='#'>" + __('Save') + "</a> " +
									"</div> " +
								"</div>");

					//init dialog events
					$("#"+dialogId).dialog({
						width: 350,
						height: 400,
						autoOpen: false,
						title: __('Manage data list')
					});

					//destroy dialog on close
					$("#"+dialogId).bind('dialogclose', function(event, ui) {
						$.tree.reference("#"+treeId).destroy();
						$("#"+dialogId).dialog('destroy');
						$("#"+dialogId).remove();
					});

					$("#"+closerId).click(function(){
						$("#"+dialogId).dialog('close');
					});

					$("#"+dialogId).bind('dialogopen', function(event, ui){
						var url 		= context.root_url + 'tao/Lists/';
						var dataUrl 		= url + 'getListsData';
						var renameUrl		= url + 'rename';
						var createUrl		= url + 'create';
						var removeListUrl	= url + 'removeList';
						var removeListEltUrl    = url + 'removeListElement';

						//create tree to manage lists
						var generisTreeInstance = $("#"+treeId).tree({
							data: {
								type: "json",
								async : true,
								opts: {
									method : "POST",
									url: dataUrl
								}
							},
							types: {
							 "default" : {
									renameable	: true,
									deletable	: true,
									creatable	: true,
									draggable	: false
								}
							},
							ui: {
								theme_name : "custom"
							},
							callback: {
								onrename: function(NODE, TREE_OBJ, RB){
									var options = {
										url: renameUrl,
										NODE: NODE,
										TREE_OBJ: TREE_OBJ
									};
									if ($(NODE).hasClass('node-instance')) {
										var PNODE = TREE_OBJ.parent(NODE);
										options.classUri = $(PNODE).prop('id');
									}

									/**
									 * Model changed, the function are not anymore static.
									 * please call renameNode on the instance of Generis Class
									 * Note : Use a GenerisTree function on a JQuery Tree ... strange
									 */
									require(['require', 'jquery', 'generis.tree.browser'], function(req, $, GenerisTreeBrowserClass) {
										GenerisTreeBrowserClass.prototype.renameNode(options);
									});
								},
								ondestroy: function(TREE_OBJ) {
									//empty and build again the list drop down on tree destroying
									$("#"+rangeId+" option").each(function(){
										if ($(this).val() !== "" && $(this).val() !== "new") {
											$(this).remove();
										}
									});
									$("#"+treeId+" .node-root .node-class").each(function(){
										$("#"+rangeId+" option[value='new']").before("<option value='"+$(this).prop('id')+"'>"+$(this).children("a:first").text()+"</option>");
									});
									$("#"+rangeId).parent("div").children("ul.form-elt-list").remove();
									$("#"+rangeId).val('');
								}
							},
							plugins: {
								//tree right click menu
								contextmenu: {
									items: {

										//create a new list or a list item
										create: {
											label: __("Create"),
											icon	: context.taobase_www + "img/add.png",
											visible: function(NODE, TREE_OBJ){
												if ($(NODE).hasClass('node-instance')) {
													return false;
												}
												return TREE_OBJ.check("creatable", NODE);
											},
											action  : function(NODE, TREE_OBJ){
												if ($(NODE).hasClass('node-class')) {
													var cssClass = 'node-instance';
													$.ajax({
														url: 	createUrl,
														type: 	"POST",
														data: 	{classUri: $(NODE).prop('id'), type: 'instance'},
														dataType: 'json',
														success: function(response){
															if (response.uri) {
																TREE_OBJ.select_branch(TREE_OBJ.create({
																	data: response.label,
																	attributes: {
																		id: response.uri,
																		'class': cssClass
																	}
																}, TREE_OBJ.get_node(NODE[0])));
															}
														}
													});
												}
												if ($(NODE).hasClass('node-root')) {
													//create list
													$.ajax({
														url: createUrl,
														type: "POST",
														data: {classUri: 'root', type: 'class'},
														dataType: 'json',
														success: function(response){
															if(response.uri){
																TREE_OBJ.select_branch(
																	TREE_OBJ.create({
																		data: response.label,
																		attributes: {
																			id: response.uri,
																			'class': 'node-class'
																		}
																	}, TREE_OBJ.get_node(NODE[0])));
															}
														}
													});
												}
												return false;
											}
										},

										//rename a node
										rename: {
											label: __("Rename"),
											icon	: context.taobase_www + "img/rename.png",
											visible: function(NODE, TREE_OBJ){
												if ($(NODE).hasClass('node-root')) {
													return false;
												}
												return TREE_OBJ.check("renameable", NODE);
											}
										},

										//remove a node
										remove: {
											label: __("Remove"),
											icon	: context.taobase_www + "img/delete.png",
											visible: function(NODE, TREE_OBJ){
												if ($(NODE).hasClass('node-root')) {
													return false;
												}
												return TREE_OBJ.check("deletable", NODE);
											},
											action  : function(NODE, TREE_OBJ){
												var removeUrl;
                                                                                                if ($(NODE).hasClass('node-root')) {
													return false;
												}
												if ($(NODE).hasClass('node-class')) {
													removeUrl = removeListUrl;
												}
												if ($(NODE).hasClass('node-instance')) {
													removeUrl = removeListEltUrl;
												}
												//remove list
												$.ajax({
													url: removeUrl,
													type: "POST",
													data: {uri: $(NODE).prop('id')},
													dataType: 'json',
													success: function(response){
														if (response.deleted) TREE_OBJ.remove(NODE);
													}
												});
												return false;
											}
										}
									}
								}
							}
						});
					});

					//open the dialog window
					$("#"+dialogId).dialog('open');
				} else {
					//load the instances and display them (the list items)
					$(this).parent("div").children("ul.form-elt-list").remove();
					var classUri = $(this).val();
					if (classUri !== '') {
						$(this).parent("div").children("div.form-error").remove();
						var elt = this;
						$.ajax({
							url: context.root_url + 'tao/Lists/getListElements',
							type: "POST",
							data: {listUri: classUri},
							dataType: 'json',
							success: function(response){
								var html = "<ul class='form-elt-list'>";
								for (i in response) {
									html += '<li>'+response[i]+'</li>';
								}
								html += '</ul>';
								$(elt).parent("div").append(html);
							}
						});
					}
				}
			 }

			 //bind functions to the drop down:

			 //display the values drop down regarding the selected type
			 $(".property-type").change(showPropertyList);
			 $(".property-type").each(showPropertyList);

			 //display the values of the selected list
			 $(".property-listvalues").change(showPropertyListValues);
			 $(".property-listvalues").each(showPropertyListValues);

			 //show the "green plus" button to manage the lists
			 $(".property-listvalues").each(function() {
				var listField = $(this);
				if (listField.parent().find('img').length === 0) {
					var listControl = $("<img title='manage lists' style='cursor:pointer;' />");
					listControl.prop('src', context.taobase_www + "img/add.png");
					listControl.click(function() {
						listField.val('new');
						listField.change();
					});
					listControl.insertAfter(listField);
				}
			 });

			 $(".property-listvalues").each(function() {
				var elt = $(this).parent("div");
				if (!elt.hasClass('form-elt-highlight') && elt.css('display') !== 'none') {
					elt.addClass('form-elt-highlight');
				}
			 });
		},

		/**
		 * controls of the translation forms
		 */
		initTranslationForm: function(){
			$('#translate_lang').change(function() {
				var trLang = $(this).val();
				if (trLang !== '') {
					$("#translation_form :input").each(function() {
						if (/^http/.test($(this).prop('name'))) {
							$(this).val('');
						}
					});
					$.post(
						getUrl('getTranslatedData'),
						{uri: $("#uri").val(), classUri: $("#classUri").val(), lang: trLang},
						function(response) {
							for (var index in response) {
								var formElt = $(":input[name='"+index+"']");
								if (formElt.hasClass('html-area')) {
									formElt.wysiwyg('setContent', response[index]);
								} else {
									formElt.val(response[index]);
								}

							}
						},
						'json'
					);
				}
			});
		},

		/**
		 * Ajax form submit -> post the form data and display back the form into the container
		 * @param myForm
		 * @return boolean
		 */
		submitForm: function(myForm) {
			
			try {
				if (myForm.prop('enctype') === 'multipart/form-data' && myForm.find(".file-uploader").length) {
					return false;
				} 
				else {
                                    var $container = helpers.getMainContainer();
                                    if(!$container || $container.length === 0){
                                        return true;//go to the link
                                    } else {
                                        $container.load(myForm.prop('action'), myForm.serializeArray());
                                    }
				}
			}
			catch (exp) {
				return false;
			}
			return false;
		}
	};

	return UiForm;
});
