define([
    'jquery',
    'lodash',
    'ui/tooltipster',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    'taoQtiItem/qtiCreator/helper/creatorRenderer',
    'taoQtiItem/qtiCreator/model/helper/container',
    'taoQtiItem/qtiCreator/editor/gridEditor/content',
    'tpl!qtiItemPic/picManager/tpl/manager',
    'css!qtiItemPicCss/pic-manager'
], function ($, _, tooltip, icRegistry, creatorRenderer, containerHelper, contentHelper, managerTpl) {
    'use strict';

    var _studentToolTag = 'student-tool';
    var _studentToolbarId = 'studentToolbar';


    /**
     * Toggle the disabled state of checkboxes
     *
     * @param $checkBoxes
     * @param state
     */
    function toggleCheckboxState($checkBoxes, state) {

        $checkBoxes.each(function() {

            // @todo this is tmp code that needs to go as soon all tools are available
            // see also further down above check event for another portion of the code
            if(this.className.indexOf('not-available') > -1) {
                return true;
            }
            // end tmp code

            this.disabled = state;
        });
    }


    /**
     * Wait for a toolbar item to be completely rendered
     *
     * @param selector
     * @returns {*}
     */
    function buttonAdded(selector) {
        var dfd = $.Deferred();
        var cnt = 1;
        var checkSelector = setInterval(function () {
            if(cnt > 25) {
                throw 'Tool takes too long to load';
            }
            if ($(selector).is(':visible')) {
                dfd.resolve();
                clearInterval(checkSelector);
            }
            cnt++;
        }, 500);

        return dfd.promise();
    }



    function itemWidgetLoaded(config, callback) {
        var $editor = config.dom.getEditorScope();
        if ($editor.data('widget')) {
            callback($editor.data('widget'));
        }
        else {
            $(document).one('widgetloaded.qticreator', function (e, item) {
                callback(item);
            });
        }
    }

    /**
     * Create a dummy place holder
     *
     * @param typeIdentifier
     * @returns {*|HTMLElement}
     */
    function getNewInfoControlNode(typeIdentifier) {
        return $('<span/>')
            .addClass('widget-box sts-tmp-element sts-placeholder-' + typeIdentifier)
            .attr('data-new', true)
            .attr('data-qti-class', 'infoControl.' + typeIdentifier);
    }


    /**
     *
     * @param config
     */
    function initStudentToolManager(config) {

        var $placeholder;

        //get item
        itemWidgetLoaded(config, function (itemWidget) {

            var item = itemWidget.element;
            var $itemPropPanel = config.dom.getItemPropertyPanel();

            //get list of all info controls available
            icRegistry.loadAll(function (allInfoControls) {

                //get item body container
                var $itemBody = config.dom.getEditorScope().find('.qti-itemBody');

                //prepare data for the tpl:
                var tools = {},
                    toolArray = [],
                    alreadySet = _.pluck(item.getElements('infoControl'), 'typeIdentifier'),
                    allInfoControlsSize,
                    $managerPanel,
                    i = 0;


                //feed the tools lists (including checked or not)
                _.each(allInfoControls, function (creator) {
                    var name = creator.getTypeIdentifier(),
                        ic = icRegistry.get(name),
                        manifest = ic.manifest,
                        controlExists = _.indexOf(alreadySet, name) > -1,
                        defaultProperties = creator.getDefaultProperties(),
                        position = defaultProperties.position || 100 + i;

                    if (manifest.disabled) {
                        return;
                    }

                    if (manifest.tags && manifest.tags[0] === _studentToolTag) {
                        tools[name] = {
                            label: manifest.label,
                            description: manifest.description,
                            checked: controlExists,
                            position: position,
                            name: name
                        };
                    }

                    // store the name also in the value for convenience
                    allInfoControls[name].name = name;

                    // determine where to position a tool on the toolbar
                    allInfoControls[name].position = position;

                    // on load we assume that everything that already exists is also checked
                    // this counts also for the toolbar which has no actual checkbox
                    allInfoControls[name].checked = controlExists;

                    // have the resources already been copied,
                    // i.e. is the tool currently installed
                    allInfoControls[name].installed = controlExists;

                    // must be false, even if tool has been installed
                    // before, resources might have changed
                    allInfoControls[name].copied = false;

                    i++;

                });

                toolArray = _.sortBy(tools, 'position');

                allInfoControlsSize = _.size(allInfoControls);

                $managerPanel = managerTpl({
                    tools: toolArray
                });
                $itemPropPanel.append($managerPanel);

                //init tooltips
                tooltip($itemPropPanel);

                //init event listeners:
                var $checkBoxes = $('[data-role="pic-manager"]').find('input:checkbox');

                $checkBoxes.on('change.picmanager', function(e) {

                    e.stopPropagation();

                    // install toolbar if required
                    if (this.checked && !allInfoControls[_studentToolbarId].installed) {
                        allInfoControls[_studentToolbarId].checked = true;
                    }

                    allInfoControls[this.name].checked = this.checked;

                    toggleCheckboxState($checkBoxes, true);

                    processAllControls();

                });


                /**
                 * Iterate over all controls and launch the actual installer/un-installer
                 */
                function processAllControls() {

                    var cnt = 0;

                    // _.forOwn callback args are value, name
                    // name is also available as value.name
                    _.forOwn(allInfoControls, function (control) {

                        // is there any action required at all?
                        // if not and if there are still items
                        // left proceed to the next one
                        if (control.checked === control.installed) {
                            cnt++;
                            return true;
                        }

                        processControl(control);

                        // break here and wait for the next call
                        // to be executed from processControl()
                        return false;
                    });

                    if(cnt === allInfoControlsSize) {
                        toggleCheckboxState($checkBoxes, false);
                    }
                }


                /**
                 * Render tool|toolbar
                 *
                 * @param elt
                 */
                function renderControl(elt) {

                    var stsClassName = (elt.typeIdentifier === _studentToolbarId) ? 'sts-scope' : 'sts-scope sts-tmp-element';

                    //add the student tool css scope
                    elt.attr('class', stsClassName);

                    elt.prop('position', allInfoControls[elt.typeIdentifier].position);
                    elt.prop('toolbarId', 'sts-' + _studentToolbarId);

                    //render it
                    elt.setRenderer(creatorRenderer.get());

                    elt.render($placeholder);


                    $placeholder = null;

                    elt.postRender({});

                    allInfoControls[elt.typeIdentifier].installed = true;

                    $.when(buttonAdded('#sts-' + elt.typeIdentifier))
                        .then(function() {
                            var widget = elt.data('widget');

                            //inform height modification
                            widget.$container.trigger('contentChange.gridEdit');
                            widget.$container.trigger('resize.gridEdit');

                            if(elt.typeIdentifier !== _studentToolbarId) {
                                toggleCheckboxState($checkBoxes, false);
                            }


                            // continue with the next element of allInfoControls
                            processAllControls();
                        });
                }


                /**
                 * Remove a student tool
                 *
                 * @param control
                 */
                function removeControl(control) {

                    var infoControls = item.getElements('infoControl'),

                        remove = function (_control) {
                            var studentTool = _.find(infoControls, {
                                typeIdentifier: _control.name
                            });
                            //call ic hook destroy() method
                            studentTool.data('pic').destroy();

                            //remove the widget from dom
                            $('#sts-' + _control.name).remove();
                            $itemBody.find('.widget-box[data-serial=' + studentTool.serial + ']').remove();

                            //remove form model
                            item.removeElement(studentTool.serial);

                            allInfoControls[_control.name].checked = false;
                            allInfoControls[_control.name].installed = false;
                        };

                    //remove it
                    remove(control);
                    // in case there only two elements left, one of them
                    // must be the toolbar and needs to be removed too
                    if (_.size(infoControls) > 2) {
                        processAllControls();
                        return;
                    }

                    // reset to checked:true on click of any tool
                    remove(allInfoControls[_studentToolbarId]);

                    toggleCheckboxState($checkBoxes, false);
                    processAllControls();
                }

                /**
                 * install|un-install a single control
                 *
                 * @param control
                 * @returns {boolean}
                 */
                function processControl(control) {

                    // what needs to be done to the control? install|uninstall
                    if (!control.checked) {
                        removeControl(control);
                        return true;
                    }

                    //create an info control (student tool|toolbar) and add it to the them
                    $placeholder = getNewInfoControlNode(control.name);
                    $itemBody.prepend($placeholder);

                    // install procedure
                    containerHelper.createElements(
                        item.getBody(),
                        contentHelper.getContent($itemBody),
                        function (newElts) {
                            // although newElts appears to be a collection it holds
                            // only _one_ element due to the callback mechanism
                            // between processControl() and processAllControls()
                            _.each(newElts, function (elt) {

                                // if the required resources have not been copied yet
                                if (!control.copied) {
                                    $.when(icRegistry.addRequiredResources(
                                            elt.typeIdentifier, config.uri))
                                        .then(function (data, textStatus, jqXHR) {

                                            if (jqXHR.status !== 200) {
                                                throw 'Failed to add required resources for the info control';
                                            }

                                            // update look-up list
                                            allInfoControls[control.name].copied = true;
                                            renderControl(elt);


                                        });
                                }
                                else {
                                    renderControl(elt);
                                }

                            });
                        });


                }

            });

        });

    }

    var pciManagerHook = {
        init: function (config) {

            //load infoControl model first into the creator renderer
            creatorRenderer
                .get()
                .load(function () {

                    initStudentToolManager(config);

                }, ['infoControl']);

        }
    };

    return pciManagerHook;
});
