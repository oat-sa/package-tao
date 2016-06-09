/**
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
 */

/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/component',
    'tpl!ui/datalist/tpl/main',
    'tpl!ui/datalist/tpl/list'
], function ($, _, __, component, mainTpl, listTpl) {
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        keyName : 'id',
        labelName : 'label',
        labelText : __('Label'),
        title : false,
        textNumber : __('Available'),
        textEmpty : __('There is nothing to list!'),
        textLoading : __('Loading'),
        selectable : false
    };

    /**
     * Defines a data list
     * @type {Object}
     */
    var datalist = {
        /**
         * Updates the list
         * @param {Array} data
         * @returns {listBox}
         * @fires datalist#update
         */
        update : function update(data) {
            var self = this;
            var controls = this.controls || {};
            var config = this.config || {};
            var $list = controls.$list;
            var $numberValue = controls.$numberValue;
            var renderData = {
                selectable : config.selectable,
                actions : config.actions,
                list : []
            };
            var list = renderData.list;
            var count;

            // disable the list while updating it
            this.setLoading(true);

            // if the update method is called before rendering, or on a destroyed component, there is no placeholder to fill...
            if ($list) {
                // be sure to remove previous list before render the new data
                $list.empty();

                if (data && data.length) {
                    // format the data to render
                    _.forEach(data, function(line) {
                        // extract the identifier and the label according to the config
                        var id = line[config.keyName];
                        var label = line[config.labelName];

                        // optional custom renderer for the label
                        if (_.isFunction(config.labelTransform)) {
                            label = config.labelTransform.call(self, label, line);
                        }

                        // the data to render only refer to id and identifier
                        list.push({
                            id: id,
                            label: label,
                            line: line  // provide the original data for dynamic behavior like hidden actions
                        });
                    });

                    // render the data at the right placeholder
                    $list.append(listTpl(renderData));

                    // update the displayed counter
                    if ($numberValue) {
                        count = data.length;

                        // optional custom renderer for the counter
                        if (_.isFunction(config.countRenderer)) {
                            count = config.countRenderer.call(self, count);
                        }

                        $numberValue.text(count);
                    }

                    // update the display status
                    this.setState('empty', false);
                    this.setState('loaded', true);
                } else {
                    // nothing to display
                    this.setState('empty', true);
                    this.setState('loaded', false);
                }

                // update the selection of existing checkboxes
                controls.$checkboxes = this.controls.$list.find('td.checkboxes input');
                controls.$massAction.toggleClass('hidden', true);
                if (this.pendingSelection) {
                    this.setSelection(this.pendingSelection);
                }
            }

            /**
             * @event datalist#update
             * @param {Array} data
             */
            self.trigger('update', data);

            // ok, the list is now ready, enable it
            this.setLoading(false);

            return this;
        },

        /**
         * Gets the current selection
         * @returns {Array}
         */
        getSelection : function getSelection() {
            var $checkboxes = this.controls && this.controls.$checkboxes;
            var selection = [];

            if ($checkboxes) {
                // extract the selection from the selected checkboxes
                $checkboxes.filter(':checked').each(function() {
                    var id = $(this).closest('tr').data('id');
                    if (id) {
                        selection.push(id);
                    }
                });
            } else {
                // the list may not already be rendered, but a selection may exist in pending state
                if (this.pendingSelection) {
                    selection = this.pendingSelection;
                }
            }
            return selection;
        },

        /**
         * Sets the current selection
         * @param {Array} selection
         * @returns {datalist}
         * @fires datalist#select
         */
        setSelection : function setSelection(selection) {
            var controls = this.controls || {};
            var $list = controls.$list;

            if ($list) {
                // be sure to discard existing selection
                controls.$checkboxes.removeAttr('checked');

                if (selection) {
                    // find each line and check it according to the provided selection
                    _.forEach(selection, function(id) {
                        $list.find('[data-id="' + id + '"] input[type="checkbox"]').attr('checked', 'checked');
                    });
                }

                // takes care of the new selection
                this._onSelection();

                // remove pending selection to avoid overwrite on next update
                this.pendingSelection = null;
            } else {
                // keep selection ready for the next update
                this.pendingSelection = selection;
            }

            return this;
        },

        /**
         * Called when a selection has been made
         * @fires datalist#select
         * @private
         */
        _onSelection : function _onSelection() {
            var controls = this.controls || {};
            var $checkboxes = controls.$checkboxes;
            var $checkAll = controls.$checkAll;
            var $checked = $checkboxes.filter(':checked');

            // update the checkAll button
            if ($checked.length === $checkboxes.length) {
                $checkAll.attr('checked', 'checked');
            } else {
                $checkAll.removeAttr('checked');
            }

            // show/hide the mass actions tools
            controls.$massAction.toggleClass('hidden', !$checked.length);

            /**
             * @event datalist#select
             * @param {Array} selection
             */
            this.trigger('select', this.getSelection());
        },

        /**
         * Sets the loading state
         * @param {Boolean} flag
         * @returns {listBox}
         */
        setLoading : function setLoading(flag) {
            if (flag) {
                this.setState('loaded', false);
            }
            return this.setState('loading', flag);
        },

        /**
         * Sets the title of the list.
         * @param {String|Boolean} title - The text to set. If the value is false no title is displayed
         * @returns {listBox}
         */
        setTitle : function setTitle(title) {
            var $title = this.controls && this.controls.$title;
            this.config.title = title;
            if ($title) {
                if (false === title) {
                    $title.addClass('hidden');
                } else {
                    $title.html(title).removeClass('hidden');
                }
            }

            return this;
        },

        /**
         * Sets the label of the number of lines.
         * @param {String|Boolean} text - The text to set. If the value is false no label is displayed
         * @returns {listBox}
         */
        setTextNumber : function setTextNumber(text) {
            var $numberLabel = this.controls && this.controls.$numberLabel;
            var $textAvailable = this.controls && this.controls.$textAvailable;
            this.config.textNumber = text;
            if ($numberLabel) {
                if(text !== false){
                    $numberLabel.html(text).removeClass('hidden');
                } else if ($textAvailable){
                    $textAvailable.addClass('hidden');
                }
            }

            return this;
        },

        /**
         * Sets the label displayed when there no data available.
         * @param {String|Boolean} text - The text to set. If the value is false no label is displayed
         * @returns {listBox}
         */
        setTextEmpty : function setTextEmpty(text) {
            var $textEmpty = this.controls && this.controls.$textEmpty;
            this.config.textEmpty = text;
            if ($textEmpty) {
                if (false === text) {
                    $textEmpty.addClass('hidden');
                } else {
                    $textEmpty.html(text).removeClass('hidden');
                }
            }

            return this;
        },

        /**
         * Sets the label displayed when the list is loading.
         * @param {String|Boolean} text - The text to set. If the value is false no label is displayed
         * @returns {listBox}
         */
        setTextLoading : function setTextLoading(text) {
            var $textLoading = this.controls && this.controls.$textLoading;
            this.config.textLoading = text;
            if ($textLoading) {
                if (false === text) {
                    $textLoading.addClass('hidden');
                } else {
                    $textLoading.html(text).removeClass('hidden');
                }
            }

            return this;
        }
    };

    /**
     * Builds an instance of the datalist component
     * @param {Object} config
     * @param {String} [config.keyName] - Sets the name of the attribute containing the identifier for each data line (default: 'id')
     * @param {String} [config.labelName] - Sets the name of the attribute containing the label for each data line (default: 'label')
     * @param {String|Boolean} [config.labelText] - Sets the displayed title for the column containing the labels. If the value is false no title is displayed (default: 'Label')
     * @param {String|Boolean} [config.title] - Sets the title of the list. If the value is false no title is displayed (default: false)
     * @param {String|Boolean} [config.textNumber] - Sets the label of the number of data lines. If the value is false no label is displayed (default: 'Available')
     * @param {String|Boolean} [config.textEmpty] - Sets the label displayed when there no data available. If the value is false no label is displayed (default: 'There is nothing to list!')
     * @param {String|Boolean} [config.textLoading] - Sets the label displayed when the list is loading. If the value is false no label is displayed (default: 'Loading')
     * @param {jQuery|HTMLElement|String} [config.renderTo] - An optional container in which renders the component
     * @param {Boolean} [config.selectable] - Append a checkbox on each displayed line to allow selection (default: false)
     * @param {Boolean} [config.replace] - When the component is appended to its container, clears the place before
     * @param {Function} [config.labelTransform] - Optional renderer applied on each displayed label.
     * @param {Function} [config.countRenderer] - An optional callback applied on the list count before display
     * @param {Array} [config.tools] - An optional list of buttons to add on top of the list. Each buttons provides a mass action on the selected lines. If selectable is not enabled, all lines are selected.
     * @param {Array} [config.actions] - An optional list of buttons to add on each line.
     * @param {Array} [data] - The data to display
     * @returns {datalist}
     *
     * @event init - Emitted when the component is initialized
     * @event destroy - Emitted when the component is destroying
     * @event render - Emitted when the component is rendered
     * @event update - Emitted when the component is updated
     * @event tool - Emitted when a tool button is clicked
     * @event action - Emitted when an action button is clicked
     * @event select - Emitted when a selection is made
     * @event show - Emitted when the component is shown
     * @event hide - Emitted when the component is hidden
     * @event enable - Emitted when the component is enabled
     * @event disable - Emitted when the component is disabled
     * @event template - Emitted when the template is changed
     */
    function datalistFactory(config, data) {
        var initConfig = config || {};
        var actions = {};
        var tools = {};

        // build a map of the tools if any
        if (initConfig.tools) {
            _.forEach(initConfig.tools, function(tool) {
                tools[tool.id] = tool;
            });
        }

        // build a map of the lines actions if any
        if (initConfig.actions) {
            _.forEach(initConfig.actions, function(action) {
                actions[action.id] = action;
            });
        }

        return component(datalist, _defaults)
            .setTemplate(mainTpl)

            // uninstalls the component
            .on('destroy', function() {
                this.controls = null;
                this.pendingSelection = null;
            })

            // renders the component
            .on('render', function() {
                var self = this;

                // get access to all needed placeholders
                this.controls = {
                    $title : this.$component.find('h1'),
                    $textEmpty : this.$component.find('.empty-list'),
                    $textAvailable : this.$component.find('.available-list'),
                    $textLoading : this.$component.find('.loading span'),
                    $numberLabel : this.$component.find('.available-list .label'),
                    $numberValue : this.$component.find('.available-list .count'),
                    $actionBar : this.$component.find('.list .action-bar'),
                    $massAction : this.$component.find('.list .mass-action'),
                    $checkAll : this.$component.find('.list th.checkboxes input'),
                    $checkboxes : this.$component.find('.list td.checkboxes input'),
                    $list : this.$component.find('.list tbody')
                };

                // take care of tools buttons
                this.controls.$actionBar.on('click', 'button', function(e) {
                    var $this = $(this);
                    var buttonId = $this.closest('button').data('control');
                    var button = tools[buttonId];
                    var selection = self.getSelection();

                    e.preventDefault();

                    if (button && button.action) {
                        button.action.call(self, selection, buttonId);
                    }

                    /**
                     * @event datalist#tool
                     * @param {Array} selection
                     * @param {String} buttonId
                     */
                    self.trigger('tool', selection, buttonId);
                });

                // take care of actions buttons
                this.controls.$list.on('click', 'button', function(e) {
                    var $this = $(this);
                    var lineId = $this.closest('tr').data('id');
                    var buttonId = $this.closest('button').data('control');
                    var button = actions[buttonId];

                    e.preventDefault();

                    if (button && button.action) {
                        button.action.call(self, lineId, buttonId);
                    }

                    /**
                     * @event datalist#action
                     * @param {String} lineId
                     * @param {String} buttonId
                     */
                    self.trigger('action', lineId, buttonId);
                });

                // take care of clicks on labels
                this.setState('selectable', this.config.selectable);
                this.controls.$list.on('click', 'td.label', function() {
                    var $checkbox;

                    if (self.config.selectable) {
                        $checkbox = $(this).closest('tr').find('input[type="checkbox"]');

                        // toggle the line selection
                        if ($checkbox.attr('checked')) {
                            $checkbox.removeAttr('checked');
                        } else {
                            $checkbox.attr('checked', 'checked');
                        }

                        // takes care of the new selection
                        self._onSelection();
                    }
                });

                // take care of clicks on checkboxes
                this.controls.$list.on('click', 'input[type="checkbox"]', function() {
                    // just takes care of the new selection
                    self._onSelection();
                });

                // check/uncheck all checkboxes
                this.controls.$checkAll.on('click', function() {
                    var $checkboxes = self.controls.$checkboxes;

                    // select/unselect all lines
                    if (this.checked) {
                        $checkboxes.attr('checked', 'checked');
                    } else {
                        $checkboxes.removeAttr('checked');
                    }

                    // takes care of the new selection
                    self._onSelection();
                });

                // data already available ?
                if (data) {
                    this.update(data);
                } else {
                    this.setState('empty', true);
                    this.setState('loaded', false);
                }
            })
            .init(initConfig);
    }

    return datalistFactory;
});
