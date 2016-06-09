/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

/**
 * UiForm class enable you to manage form elements, initialize form component and bind common events
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
define([
    'module',
    'jquery',
    'i18n',
    'helpers',
    'context',
    'form/property',
    'form/post-render-props',
    'util/encode',
    'jwysiwyg' ],
    function (
        module,
        $,
        __,
        helpers,
        context,
        property,
        postRenderProps,
        encode
        ) {

    'use strict';

        /**
         * Create a URL based on action and module
         *
         * @param action
         * @returns {string}
         */
        function getUrl(action) {
            var conf = module.config();
            return context.root_url + conf.extension + '/' + conf.module + '/' + action;
        }

    var UiForm = {
        init: function () {
            var self = this;
            this.counter = 0;
            this.initGenerisFormPattern = new RegExp(['add', 'edit', 'mode', 'PropertiesAuthoring'].join('|'), 'i');
            this.initTranslationFormPattern = /translate/;

            $("body").ajaxComplete(function (event, request, settings) {
                var testedUrl;
                //initialize regarding the requested action
                //async request waiting for html or not defined
                if (settings.dataType === 'html' || !settings.dataType) {
                    if (settings.url.indexOf('?') !== -1) {
                        testedUrl = settings.url.substr(0, settings.url.indexOf('?'));
                    }
                    else {
                        testedUrl = settings.url;
                    }

                    self.initRendering();
                    self.initElements();
                    if (self.initGenerisFormPattern.test(testedUrl)) {
                        self.initOntoForms();
                    }
                    if (self.initTranslationFormPattern.test(testedUrl)) {
                        self.initTranslationForm();
                    }
                }
            });
            this.initRendering();
        },

        /**
         * make some adjustment on the forms
         */
        initRendering: function () {

            var self = this;

            var $container          = $('.content-block .xhtml_form:first'),
                $toolBar            = $container.find('.form-toolbar'),
                $authoringBtn       = $('.authoringOpener'),
                $authoringBtnParent,
                $testAuthoringBtn   = $('.test-authoring'),
                $rdfImportForm      = $('.rdfImport #import'),
                $rdfExportForm      = $('.rdfExport #export');

            // allows to fix label position for list of radio buttons
            $('.form_desc ~.form_radlst').parent().addClass('bool-list');

            // allows long labels if the following input is hidden
            $('.form_desc + input[type="hidden"]').prev().addClass('hidden-input-label');

            // move authoring button to toolbar, unless it is already there
            if($authoringBtn.length && !$authoringBtn.hasClass('btn-info')) {
                $authoringBtnParent = $authoringBtn.parent();
                $authoringBtn.prepend($('<span>', { 'class': 'icon-edit' }));
                $authoringBtn.addClass('btn-info small');
                $authoringBtn.appendTo($toolBar);
                $authoringBtnParent.remove();
            }

            // move test authoring button
            if($testAuthoringBtn.length) {
                $testAuthoringBtn.prependTo($toolBar);
            }

            // import Ontology styling changes
            if($rdfImportForm.length) {
                $('span.form_desc:empty',$rdfImportForm).hide();
                $('span.form-elt-info',$rdfImportForm).css({
                    display: 'block',
                    width: '100%'
                });
                $('.form-elt-container.file-uploader',$rdfImportForm).css({
                    width: '65%',
                    float: 'right'
                });

            }
            if($rdfExportForm.length){
                $('div:first',$rdfExportForm).find('input[type="text"]').css('width', 'calc(65% - 23px)');
                $('div:not(.form-toolbar):last span',$rdfExportForm).css('float', 'right')
                                                                    .closest('div')
                                                                    .find('[id*="ns_filter"]')
                                                                    .addClass('btn-default small');
            }

            $('body').off('submit','.xhtml_form form').on('submit', '.xhtml_form form', function (e) {
                e.preventDefault();
                var $form = $(this),
                    formData = self.getFormData($form);
                return self.submitForm($form, formData);
            });

            $('.form-submitter').off('click').on('click', function (e) {
                e.preventDefault();
                $(e.target).closest('.xhtml_form form').trigger('submit');
            });

            // modify properties
            postRenderProps.init();
        },

        /**
         * Retrieve form fields and pack to internal format for transfering
         * @param {jQueryElement} $form
         * @returns {object|undefined}
         */
        getFormData: function ($form) {

            //for backward compatibility
            if (!$('[id="tao.forms.class"]').length) {
                return;
            }

            var formData = {},
                clazz = {};

            //get all global data
            $('input.global', $form[0]).each(function () {
                var $global = $(this);
                var name = $global.attr('name');
                if (name.indexOf('class_') > -1) {
                    name = name.replace('class_', '');
                    clazz[name] = $global.val();

                }
                else {
                    formData[name] = $global.val();
                }
            });
            if (clazz.length !== 0) {
                formData.class = clazz;
            }

            var properties = [];
            //get data for each property
            $('.regular-property', $form[0]).each(function () {
                var property = {};
                var name = '';

                //get range on advanced mode
                var range = [];
                $('[id*="http_2_www_0_w3_0_org_1_2000_1_01_1_rdf-schema_3_range-TreeBox"]', this).find('.checked').each(function () {
                    range.push($(this).parent().attr('id'));
                });
                if (range.length !== 0) {
                    property['http_2_www_0_w3_0_org_1_2000_1_01_1_rdf-schema_3_range'] = range;
                }

                $(':input.property', this).each(function () {
                    var $property = $(this);
                    name = $property.attr('name').replace(/(property_)?[^_]+_/, '');
                    if ($property.attr('type') === 'radio') {
                        if ($property.is(':checked')) {
                            property[name] = $property.val();
                        }
                    }
                    else {
                        property[name] = $property.val();
                    }

                });
                //get data for each index
                var indexes = [];
                $(':input.index', this).each(function () {

                    var i;
                    var found = false;
                    var name = '';
                    var $index = $(this);
                    for (i in indexes) {
                        if (indexes[i] && $index.attr('data-related-index') === indexes[i].uri) {
                            name = $index.attr('name').replace(/(index_)?[^_]+_/, '');
                            if ($index.attr('type') === 'radio' || $index.attr('type') === 'checkbox') {
                                if ($index.is(':checked')) {
                                    indexes[i][name] = $index.val();
                                }
                            }
                            else {
                                indexes[i][name] = $index.val();
                            }

                            found = true;
                        }
                    }
                    if (!found) {
                        var index = {};
                        index.uri = $index.attr('data-related-index');
                        name = $index.attr('name').replace(/(index_)?[^_]+_/, '');
                        if ($index.attr('type') === 'radio') {
                            if ($index.is(':checked')) {
                                index[name] = $index.val();
                            }
                        }
                        else {
                            index[name] = $index.val();
                        }
                        indexes.push(index);
                    }


                });
                //add indexes to related property
                property.indexes = indexes;
                properties.push(property);
            });

            formData.properties = properties;

            return formData;
        },

        initElements: function () {

            //revert form button
            $(".form-refresher").off('click').on('click', function () {
                var $form = $(this).parents('form');
                $(":input[name='" + $form.attr('name') + "_sent']").remove();

                return $form.submit();
            });

            //translate button
            var $uriElm      = $("#uri"),
                $classUriElm = $("#classUri");

            $(".form-translator").off('click').on('click', function () {
                if ( $uriElm.length && $classUriElm.length) {
                    helpers.getMainContainer().load(getUrl('translateInstance'), {'uri': $uriElm.val(), 'classUri': $classUriElm.val()});
                }
                return false;
            });

            //map the wysiwyg editor to the html-area fields
            $('.html-area').each(function () {
                if ($(this).css('display') !== 'none') {
                    $(this).wysiwyg({'css': context.taobase_www + 'css/layout.css'});
                }
            });

            $('.box-checker').off('click').on('click', function () {
                var $checker = $(this);
                var regexpId = new RegExp('^' + $checker.prop('id').replace('_checker', ''), 'i');

                if ($checker.hasClass('box-checker-uncheck')) {
                    $(":checkbox").each(function () {
                        if (regexpId.test(this.id)) {
                            //noinspection JSPotentiallyInvalidUsageOfThis,JSPotentiallyInvalidUsageOfThis
                            this.checked = false;
                        }
                    });
                    $checker.removeClass('box-checker-uncheck');
                    $checker.text(__('Check all'));
                }
                else {
                    $(":checkbox").each(function () {
                        if (regexpId.test(this.id)) {
                            this.checked = true;
                        }
                    });
                    $checker.addClass('box-checker-uncheck');
                    $checker.text(__('Uncheck all'));
                }

                return false;
            });
        },

        /**
         * init special forms controls
         */
        initOntoForms: function () {


            //open the authoring tool on the authoringOpener button
            $('.authoringOpener').click(function () {
                var tabUrl = getUrl('authoring'),
                    tabId = 'panel-' + module.config().module.toLowerCase() + '_authoring',
                    $tabContainer = $('#tabs'),
                    $panel = (function() {
                        var $wantedPanel = $tabContainer.find('#' + tabId);

                        if(!$wantedPanel.length) {
                            $wantedPanel = $('<div>', { id: tabId, 'class': 'clear content-panel' }).hide();
                            $tabContainer.find('.content-panel').after($wantedPanel);
                        }
                        return $wantedPanel;
                    }());

                $.ajax({
                    type: "GET",
                    url: tabUrl,
                    data: {
                        uri: $("#uri").val(),
                        classUri: $("#classUri").val()
                    },
                    dataType: 'html',
                    success: function (responseHtml) {
                        $tabContainer.find('.content-panel').not($panel).hide();
                        window.location.hash = tabId;
                        responseHtml = $(responseHtml);
                        responseHtml.find('#authoringBack').click(function () {
                            var $myPanel = $(this).parents('.content-panel'),
                                $otherPanel = $myPanel.prev();
                            $myPanel.hide();
                            $otherPanel.show();
                        });
                        $panel.html(responseHtml).show();
                    }
                });
            });

            $('input.editVersionedFile').each(function () {
                var infoUrl = context.root_url + 'tao/File/getPropertyFileInfo';
                var data = {
                    'uri': $("#uri").val(),
                    'propertyUri': $(this).siblings('label.form_desc').prop('for')
                };
                var $_this = $(this);
                $.ajax({
                    type: "GET",
                    url: infoUrl,
                    data: data,
                    dataType: 'json',
                    success: function (r) {
                        $_this.after('<span>' + r.name + '</span>');
                    }
                });
            }).click(function () {
                var data = {
                    'uri': $("#uri").val(),
                    'propertyUri': $(this).siblings('label.form_desc').prop('for')
                };

                helpers.getMainContainer().load(getUrl('editVersionedFile'), data);
                return false;
            });

            /**
             * remove a form group, ie. a property
             */
            function removePropertyGroup() {
                if (confirm(__('Please confirm property deletion!'))) {
                    var $groupNode = $(this).closest(".form-group");
                    property.remove($(this).data("uri"), $("#id").val(), helpers._url('removeClassProperty', 'PropertiesAuthoring', 'tao'),function(){
                        $groupNode.remove();
                    });
                }
            }

            //property delete button
            $(".property-deleter").off('click').on('click', removePropertyGroup);

            //property add button
            $(".property-adder").off('click').on('click', function (e) {
                e.preventDefault();
                property.add($("#id").val(), helpers._url('addClassProperty', 'PropertiesAuthoring', 'tao'));
            });

            $(".index-adder").off('click').on('click', function (e) {
                e.preventDefault();
                var $prependTo = $(this).closest('div');
                var $groupNode = $(this).closest(".form-group");
                if ($groupNode.length) {
                    var max = 0;
                    var $propertyindex = $('.property-uri', $groupNode);
                    var propertyindex = parseInt($propertyindex.attr('id').replace(/[\D]+/, ''));


                    $groupNode.find('[data-index]').each(function(){
                        if(max < $(this).data('index')){
                            max = $(this).data('index');
                        }
                    });

                    max = max + 1;
                    var uri = $groupNode.find('.property-uri').val();
                    $.ajax({
                        type: "GET",
                        url: helpers._url('addPropertyIndex', 'PropertiesAuthoring', 'tao'),
                        data: {uri : uri, index : max, propertyIndex : propertyindex},
                        dataType: 'json',
                        success: function (response) {
                            $prependTo.before(response.form);
                        }
                    });
                }
            });

            $('.property-edit-container').off('click', '.index-remover').on('click', '.index-remover', function(e){
                e.preventDefault();
                var $groupNode = $(this).closest(".form-group");
                var uri = $groupNode.find('.property-uri').val();

                var $editContainer = $($groupNode[0]).children('.property-edit-container');
                $.ajax({
                    type: "POST",
                    url: helpers._url('removePropertyIndex', 'PropertiesAuthoring', 'tao'),
                    data: {uri : uri, indexProperty : $(this).attr('id')},
                    dataType: 'json',
                    success: function (response) {
                        var $toRemove = $('[id*="'+response.id+'"], [data-related-index="'+response.id+'"]');
                        $toRemove.each(function(){
                            var $currentTarget = $(this);
                            while(!_.isEqual($currentTarget.parent()[0], $editContainer[0]) && $currentTarget.parent()[0] !== undefined){
                                $currentTarget = $currentTarget.parent();
                            }
                            $currentTarget.remove();
                        });
                    }
                });
            });

            $(".property-mode").off('click').on('click', function () {
                var $btn = $(this),
                    mode = 'simple';

                if ($btn.hasClass('disabled')) {
                    return;
                }

                if ($btn.hasClass('property-mode-advanced')) {
                    mode = 'advanced';
                }
                var url = $btn.parents('form').prop('action');

                helpers.getMainContainer().load(url, {
                    'property_mode': mode,
                    'uri': $("#uri").val(),
                    'id': $("#id").val(),
                    'classUri': $("#classUri").val()
                });

                return false;
            });

            /**
             * display or not the list regarding the property type
             */
            function showPropertyList() {
                var $this = $(this);
                var $elt = $this.parent("div").next("div");
                var propertiesTypes = ['list','tree'];

                var re = new RegExp(propertiesTypes.join('$|').concat('$'));
                if (re.test($this.val())) {
                    if ($elt.css('display') === 'none') {
                        $elt.show();
                        $elt.find('select').removeAttr('disabled');

                    }
                }
                else if ($elt.css('display') !== 'none') {
                    $elt.css('display', 'none');
                    $elt.find('select').prop('disabled', "disabled");
                    $elt.find('select option[value=" "]').attr('selected',true);
                }

                $.each(propertiesTypes, function (i, rangedPropertyName) {
                    var re = new RegExp(rangedPropertyName + '$');
                    if (re.test($this.val())) {
                        $elt.find('select').html($elt.closest('.property-edit-container').find('.' + rangedPropertyName + '-template').html());
                        return true;
                    }
                })
            }

            /**
             * by selecting a list, the values are displayed or the list editor opens
             */
            function showPropertyListValues() {
                var $this = $(this);
                var elt = $this.parent("div");
                if ($this.val() === 'new') {
                    //Open the list editor: a tree in a dialog popup
                    var rangeId = $this.prop('id');
                    var dialogId = rangeId.replace('_range', '_dialog');
                    var treeId = rangeId.replace('_range', '_tree');
                    var closerId = rangeId.replace('_range', '_closer');

                    //dialog content to embed the list tree
                    elt.append("<div id='" + dialogId + "' style='display:none;' > " +
                        "<span class='ui-state-highlight' style='margin:15px;'>" + __('Right click the tree to manage your lists') + "</span><br /><br />" +
                        "<div id='" + treeId + "' ></div> " +
                        "<div style='text-align:center;margin-top:30px;'> " +
                        "<a id='" + closerId + "' class='ui-state-default ui-corner-all' href='#'>" + __('Save') + "</a> " +
                        "</div> " +
                        "</div>");

                    //init dialog events
                    var $dialogElm = $("#" + dialogId);
                    $dialogElm.dialog({
                        width: 350,
                        height: 400,
                        autoOpen: false,
                        title: __('Manage data list')
                    });

                    //destroy dialog on close
                    $dialogElm.bind('dialogclose', function (event, ui) {
                        $.tree.reference("#" + treeId).destroy();
                        $dialogElm.dialog('destroy');
                        $dialogElm.remove();
                    });

                    $("#" + closerId).click(function () {
                        $("#" + dialogId).dialog('close');
                    });

                    $dialogElm.bind('dialogopen', function (event, ui) {
                        var url = context.root_url + 'taoBackOffice/Lists/';
                        var dataUrl = url + 'getListsData';
                        var renameUrl = url + 'rename';
                        var createUrl = url + 'create';
                        var removeListUrl = url + 'removeList';
                        var removeListEltUrl = url + 'removeListElement';

                        //create tree to manage lists
                        $("#" + treeId).tree({
                            data: {
                                type: "json",
                                async: true,
                                opts: {
                                    method: "POST",
                                    url: dataUrl
                                }
                            },
                            types: {
                                "default": {
                                    renameable: true,
                                    deletable: true,
                                    creatable: true,
                                    draggable: false
                                }
                            },
                            ui: {
                                theme_name: "custom"
                            },
                            callback: {
                                onrename: function (NODE, TREE_OBJ, RB) {
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
                                    require(['require', 'jquery', 'generis.tree.browser'], function (req, $, GenerisTreeBrowserClass) {
                                        GenerisTreeBrowserClass.prototype.renameNode(options);
                                    });
                                },
                                ondestroy: function (TREE_OBJ) {
                                    var $rangeElm = $("#" + rangeId);

                                    //empty and build again the list drop down on tree destroying
                                    $rangeElm.find('option').each(function () {
                                        var $option = $(this);
                                        if ($option.val() !== "" && $option.val() !== "new") {
                                            $option.remove();
                                        }
                                    });
                                    $("#" + treeId + " .node-root .node-class").each(function () {
                                        $rangeElm.find("option[value='new']").before("<option value='" + $(this).prop('id') + "'>" + $(this).children("a:first").text() + "</option>");
                                    });
                                    $rangeElm.parent("div").children("ul.form-elt-list").remove();
                                    $rangeElm.val('');
                                }
                            },
                            plugins: {
                                //tree right click menu
                                contextmenu: {
                                    items: {

                                        //create a new list or a list item
                                        create: {
                                            label: __("Create"),
                                            icon: context.taobase_www + "img/add.png",
                                            visible: function (NODE, TREE_OBJ) {
                                                if ($(NODE).hasClass('node-instance')) {
                                                    return false;
                                                }
                                                return TREE_OBJ.check("creatable", NODE);
                                            },
                                            action: function (NODE, TREE_OBJ) {
                                                if ($(NODE).hasClass('node-class')) {
                                                    var cssClass = 'node-instance';
                                                    $.ajax({
                                                        url: createUrl,
                                                        type: "POST",
                                                        data: {classUri: $(NODE).prop('id'), type: 'instance'},
                                                        dataType: 'json',
                                                        success: function (response) {
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
                                                        success: function (response) {
                                                            if (response.uri) {
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
                                            icon: context.taobase_www + "img/rename.png",
                                            visible: function (NODE, TREE_OBJ) {
                                                if ($(NODE).hasClass('node-root')) {
                                                    return false;
                                                }
                                                return TREE_OBJ.check("renameable", NODE);
                                            }
                                        },

                                        //remove a node
                                        remove: {
                                            label: __("Remove"),
                                            icon: context.taobase_www + "img/delete.png",
                                            visible: function (NODE, TREE_OBJ) {
                                                if ($(NODE).hasClass('node-root')) {
                                                    return false;
                                                }
                                                return TREE_OBJ.check("deletable", NODE);
                                            },
                                            action: function (NODE, TREE_OBJ) {
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
                                                    success: function (response) {
                                                        if (response.deleted) {
                                                            TREE_OBJ.remove(NODE);
                                                        }
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
                    $dialogElm.dialog('open');
                }
                else {
                    //load the instances and display them (the list items)
                    $(elt).parent("div").children("ul.form-elt-list").remove();
                    var classUri = $this.val();
                    if (classUri !== '' && classUri !== ' ') {
                        $this.parent("div").children("div.form-error").remove();
                        //var elt = this;
                        $.ajax({
                            url: context.root_url + 'taoBackOffice/Lists/getListElements',
                            type: "POST",
                            data: {listUri: classUri},
                            dataType: 'json',
                            success: function (response) {
                                var html = "<ul class='form-elt-list'>",
                                    property;
                                for (property in response) {
                                    if(!response.hasOwnProperty(property)) {
                                        continue;
                                    }
                                    html += '<li>' + encode.html(response[property]) + '</li>';
                                }
                                html += '</ul>';
                                $(elt).parent("div").append(html);
                            }
                        });
                    }
                }
            }

            //bind functions to the drop down:

            $('.property-template').each(function(){
                $(this).closest('div').hide();
            });

            //display the values drop down regarding the selected type
            var $propertyType = $(".property-type"),
                $propertyListValues = $(".property-listvalues");

            $propertyType.on('change', showPropertyList).trigger('change');

            //display the values of the selected list
            $propertyListValues.on('change', showPropertyListValues).trigger('change');

            //show the "green plus" button to manage the lists
            $propertyListValues.each(function () {
                var listField = $(this);
                if (listField.parent().find('img').length === 0) {
                    var listControl = $("<img title='manage lists' class='manage-lists' style='cursor:pointer;' />");
                    listControl.prop('src', context.taobase_www + "img/add.png");
                    listControl.click(function () {
                        listField.val('new');
                        listField.change();
                    });
                    listControl.insertAfter(listField);
                }
            });

            $propertyListValues.each(function () {
                var elt = $(this).parent("div");
                if (!elt.hasClass('form-elt-highlight') && elt.css('display') !== 'none') {
                    elt.addClass('form-elt-highlight');
                }
            });
        },

        /**
         * controls of the translation forms
         */
        initTranslationForm: function () {
            $('#translate_lang').change(function () {
                var trLang = $(this).val();
                if (trLang !== '') {
                    $("#translation_form").find(":input").each(function () {
                        if (/^http/.test($(this).prop('name'))) {
                            $(this).val('');
                        }
                    });
                    $.post(
                        getUrl('getTranslatedData'),
                        {uri: $("#uri").val(), classUri: $("#classUri").val(), lang: trLang},
                        function (response) {
                            for (var index in response) {
                                var formElt = $(":input[name='" + index + "']");
                                if (formElt.hasClass('html-area')) {
                                    formElt.wysiwyg('setContent', response[index]);
                                }
                                else {
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
         * @param serialize
         * @return boolean
         */
        submitForm: function (myForm, serialize) {

            try {
                if (myForm.prop('enctype') === 'multipart/form-data' && myForm.find(".file-uploader").length) {
                    return false;
                }
                else {
                    //FIXME should use sectionAPI instead
                    var $container = myForm.closest('.content-block');
                    if (!$container || $container.length === 0) {
                        return true;//go to the link
                    }
                    else {
                        serialize = typeof serialize !== 'undefined' ? serialize : myForm.serializeArray();
                        $container.load(myForm.prop('action'), serialize);
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
