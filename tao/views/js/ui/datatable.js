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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 */

define([
    'jquery',
    'lodash',
    'i18n',
    'core/pluginifier',
    'tpl!ui/datatable/tpl/layout'
], function($, _, __, Pluginifier, layout){

    'use strict';

    var ns = 'datatable';

    var dataNs = 'ui.' + ns;

    var defaults = {
        start: 0,
        rows: 25,
        page: 1,
        sortby: 'id',
        sortorder: 'asc'
    };

    /**
     * The CSS class used to hide an element
     * @type {String}
     */
    var hiddenCls = 'hidden';

    /**
     * The dataTable component makes you able to browse items and bind specific
     * actions to undertake for edition and removal of them.
     *
     * Parameters that will be send to backend by component:
     *
     * Pagination
     * @param {Number} rows - count of rows, that should be returned from backend, in other words limit.
     * @param {Number} page - number of page, that should be requested.
     *
     * Sorting
     * @param {String} sortby - name of column
     * @param {String} sortorder - order of sorting, can be 'asc' or 'desc' for ascending sorting and descending sorting respectively.
     *
     * Filtering
     * @param {String} filterquery - query string for filtering of rows.
     * @param {String[]} filtercolumns[] - array of columns, in which will be implemented search during filtering process.
     * For column filter it will be only one item with column name, but component has ability define list of columns for default filter (in top toolbar).
     * Backend should correctly receive this list of columns and do search in accordance with this parameters.
     * By default, columns are not defined, so this parameter not will be sent. If filtercolumns[] not exists, backend should search by all columns.
     *
     * @example of query (GET): rows=25&page=1&sortby=login&sortorder=asc&filterquery=loginame&filtercolumns[]=login
     *
     * @exports ui/datatable
     */
    var dataTable = {

        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable([], {});
         *
         * @constructor
         * @param {Object} options - the plugin options.
         * @param {String} options.url - the URL of the service used to retrieve the resources.
         * @param {Object[]} options.model - the model definition.
         * @param {Function} options.actions.xxx - the callback function for items xxx, with a single parameter representing the identifier of the items.
         * @param {Function} options.listeners.xxx - the callback function for event xxx, parameters depends to event trigger call.
         * @param {Boolean} options.selectable - enables the selection of rows using checkboxes.
         * @param {Boolean} options.rowSelection - enables the selection of rows by clicking on them.
         * @param {Object} options.tools - a list of tool buttons to display above the table.
         * @param {Object|Boolean} options.status - allow to display a status bar.
         * @param {Object|Boolean} options.filter - allow to display a filter bar.
         * @param {String[]} options.filter.columns - a list of columns that will be used for default filter. Can be overridden by column filter.
         * @param {String} options.filterquery - a query string for filtering, using only in runtime.
         * @param {String[]} options.filtercolumns - a list of columns, in that should be done search, using only in runtime.
         * @param {Object} [data] - inject predefined data to avoid the first query.
         * @fires dataTable#create.datatable
         * @returns {jQueryElement} for chaining
         */
        init: function(options, data) {

            var self = dataTable;
            options = _.defaults(options, defaults);

            return this.each(function() {
                var $elt = $(this);
                var currentOptions = $elt.data(dataNs);

                if (!currentOptions) {
                    //add data to the element
                    $elt.data(dataNs, options);

                    $elt.one('load.' + ns , function(){
                        /**
                         * @event dataTable#create.datatable
                         */
                        $elt.trigger('create.' + ns);
                    });

                    if (data) {
                        self._render($elt, data);
                    } else {
                        self._query($elt);
                    }
                } else {
                    // update existing options
                    if (options) {
                        $elt.data(dataNs, _.merge(currentOptions, options));
                    }

                    self._refresh($elt, data);
                }
            });
        },

        /**
         * Refresh the data table using current options
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable('refresh');
         *
         * @param {jQueryElement} $elt - plugin's element
         * @param {Object} [data] - Data to render immediately, prevents the query to be made.
         */
        _refresh: function($elt, data) {
            // TODO: refresh only rows with data, not all component
            if (data) {
                this._render($elt, data);
            } else {
                this._query($elt);
            }
        },

        /**
         * Query the server for data and load the table.
         *
         * @private
         * @param {jQueryElement} $elt - plugin's element
         * @fires dataTable#query.datatable
         */
        _query: function($elt) {

            var self = this;
            var options = $elt.data(dataNs);
            var parameters = _.merge({},_.pick(options, ['rows', 'page', 'sortby', 'sortorder']), options.params || {});
            var ajaxConfig = {
                url: options.url,
                data: parameters,
                dataType : 'json',
                type: options.querytype || 'GET'
            };

            // add current filter if any
            if (options.filter && options.filterquery) {
                ajaxConfig.data.filterquery = options.filterquery;
            }

            // add columns for filter if any
            if (options.filter && options.filtercolumns) {
                ajaxConfig.data.filtercolumns = options.filtercolumns;
            }

            /**
             * @event dataTable#query.datatable
             * @param {Object} ajaxConfig - The config object used to setup the AJAX request
             */
            $elt.trigger('query.' + ns, [ajaxConfig]);

            // display the loading state
            if (options.status) {
                $elt.find('.loading').removeClass(hiddenCls);
            }

            $.ajax(ajaxConfig).done(function(response) {
                self._render($elt, response);
            });
        },

        /**
         * Renders the table using the provided data set
         *
         * @param {jQueryElement} $elt - plugin's element
         * @param {Object} dataset - the data set to render
         * @private
         * @fires dataTable#beforeload.datatable
         * @fires dataTable#load.datatable
         */
        _render: function($elt, dataset) {
            var self = this;
            var options = $elt.data(dataNs);
            var $rendering;
            var $statusEmpty;
            var $statusAvailable;
            var $statusCount;
            var $forwardBtn;
            var $backwardBtn;
            var $sortBy;
            var $sortElement;
            var $checkAll;
            var $checkboxes;
            var $massActionBtns = $();
            var $rows;
            var amount;
            var join = function join(input) {
                return typeof input !== 'object' ? input : input.join(', ');
            };

            dataset = dataset || {};

            // overrides column options
            _.forEach(options.model, function (field) {
                if (!options.filter) {
                    field.filterable = false;
                }
                if (field.transform) {
                    field.transform = _.isFunction(field.transform) ? field.transform : join;
                }
            });

            if (options.sortby) {
                options = this._sortOptions($elt, options.sortby, options.sortorder);
            }

            // process data by model rules
            if (_.some(options.model, 'transform')) {
                var transforms = _.where(options.model, 'transform');
                _.forEach(dataset.data, function (row, index) {
                    _.forEach(transforms, function (field) {
                        row[field.id] = field.transform(row[field.id], row, field, index, dataset.data);
                    });
                });
            }

            /**
             * @event dataTable#beforeload.datatable
             * @param {Object} dataset - The data set object used to render the table
             */
            $elt.trigger('beforeload.' + ns, [dataset]);

            // Call the rendering
            $rendering = $(layout({options: options, dataset: dataset}));

            // the readonly property contains an associative array where keys are the ids of the items (lines)
            // the value can be a boolean (true for disable buttons, false to enable)
            // it can also bo an array that let you disable/enable the action you want
            // readonly = {
            //  id1 : {'view':true, 'delete':false},
            //  id2 : true
            //}
            _.forEach(dataset.readonly, function(values, id){
                if(values === true){
                    $('[data-item-identifier="'+id+'"] button', $rendering).addClass('disabled');
                }
                else if(values && typeof values === 'object'){
                    for (var action in values) {
                        if (values.hasOwnProperty(action)) {
                            if(values[action] === true){
                                $('[data-item-identifier="'+id+'"] button.'+action, $rendering).addClass('disabled');
                            }
                        }
                    }
                }
            });

            // Attach a listener to every action button created
            _.forEach(options.actions, function(action, name){
                var css;

                if (!_.isFunction(action)) {
                    name = action.id || name;
                    action = action.action || function() {};
                }

                css = '.' + name;

                $rendering
                    .off('click', css)
                    .on('click', css, function(e) {
                        var $btn = $(this);
                        e.preventDefault();
                        if (!$btn.hasClass('disabled')) {
                            action.apply($btn, [$btn.closest('[data-item-identifier]').data('item-identifier')]);
                        }
                    });
            });

            // Attach a listener to every tool button created
            _.forEach(options.tools, function(action, name) {

                var massAction = true;
                var css;

                if (!_.isFunction(action)) {
                    name = action.id || name;
                    massAction = action.massAction;
                    action = action.action || function() {};
                }

                css = '.tool-' + name;
                if (massAction) {
                    $massActionBtns = $massActionBtns.add($rendering.find(css));
                }

                $rendering
                    .off('click', css)
                    .on('click', css, function(e) {
                        var $btn = $(this);
                        e.preventDefault();
                        if (!$btn.hasClass('disabled')) {
                            action.apply($btn, [self._selection($elt)]);
                        }
                    });
            });

            // bind listeners to events
            _.forEach(options.listeners, function (callback, event) {
                var ev = [event, ns].join('.');
                $elt
                    .off(ev)
                    .on(ev, callback);
            });

            // Now $rendering takes the place of $elt...
            $rows = $rendering.find('tbody tr');
            $forwardBtn = $rendering.find('.datatable-forward');
            $backwardBtn = $rendering.find('.datatable-backward');
            $sortBy = $rendering.find('th [data-sort-by]');
            $sortElement = $rendering.find('[data-sort-by="'+ options.sortby +'"]');
            $checkAll = $rendering.find('th.checkboxes input');
            $checkboxes = $rendering.find('td.checkboxes input');

            if (options.rowSelection) {
                $('table.datatable', $rendering).addClass('hoverable');
                $rendering.on('click', 'tbody td', function (e) {
                    // exclude from processing columns with actions
                    if (($(e.target).hasClass('checkboxes') || $(e.target).hasClass('actions'))) {
                        return false;
                    }

                    var currentRow = $(this).parent();

                    $rows.removeClass('selected');
                    currentRow.toggleClass('selected');

                    $elt.trigger('selected.' + ns,
                        _.where(dataset.data, {id: currentRow.data('item-identifier')})
                    );
                });
            }

            $forwardBtn.click(function() {
                self._next($elt);
            });

            $backwardBtn.click(function() {
                self._previous($elt);
            });

            $sortBy.click(function() {
                var column = $(this).data('sort-by');
                self._sort($elt, column);
            });

            // Add the filter behavior
            if (options.filter) {
                _.forEach($rendering.find('.filter'), function ($filter) {

                    var $filterInput = $('input', $filter);
                    var $filterBtn = $('button', $filter);
                    var column = $($filter).data('column');
                    var filterColumns = options.filtercolumns ? options.filtercolumns : [];

                    // set value to filter field
                    if (options.filterquery) {
                        if (column === filterColumns.join()) {
                            $filterInput.val(options.filterquery).addClass('focused');
                        }
                    }

                    // clicking the button trigger the request
                    $filterBtn.off('click').on('click', function(e) {
                        e.preventDefault();
                        self._filter($elt, $filter, column ? column.split(',') : options.filter.columns);
                    });

                    // or press ENTER
                    $filterInput.off('keypress').on('keypress', function(e) {
                        if (e.which === 13) {
                            e.preventDefault();
                            self._filter($elt, $filter, column ? column.split(',') : options.filter.columns);
                        }
                    });
                });
            }

            // check/uncheck all checkboxes
            $checkAll.click(function() {
                if (this.checked) {
                    $checkAll.attr('checked', 'checked');
                    $checkboxes.attr('checked', 'checked');
                } else {
                    $checkAll.removeAttr('checked');
                    $checkboxes.removeAttr('checked');
                }

                if ($massActionBtns.length) {
                    $massActionBtns.toggleClass('invisible', !$checkboxes.filter(':checked').length);
                }

                /**
                 * @event dataTable#select.dataTable
                 */
                $elt.trigger('select.' + ns);
            });

            // when check/uncheck a box, toggle the check/uncheck all
            $checkboxes.click(function() {
                var $checked = $checkboxes.filter(':checked');
                if ($checked.length === $checkboxes.length) {
                    $checkAll.attr('checked', 'checked');
                } else {
                    $checkAll.removeAttr('checked');
                }

                if ($massActionBtns.length) {
                    $massActionBtns.toggleClass('invisible', !$checkboxes.filter(':checked').length);
                }

                /**
                 * @event dataTable#select.dataTable
                 */
                $elt.trigger('select.' + ns);
            });

            // Remove sorted class from all th
            $('th.sorted',$rendering).removeClass('sorted');
            // Add the sorted class to the sorted element and the order class
            $sortElement.addClass('sorted').addClass('sorted_'+options.sortorder);

            if (!dataset.page || dataset.page === 1) {
                $backwardBtn.attr('disabled', '');
            } else {
                $backwardBtn.removeAttr('disabled');
            }

            if (dataset.page >= dataset.total) {
                $forwardBtn.attr('disabled', '');
            } else {
                $forwardBtn.removeAttr('disabled');
            }

            // Update the status
            if (options.status) {
                $statusEmpty = $rendering.find('.empty-list');
                $statusAvailable = $rendering.find('.available-list');
                $statusCount = $statusAvailable.find('.count');

                $rendering.find('.loading').addClass(hiddenCls);

                // when the status is enabled, the response must contain the total amount of records
                amount = dataset.amount || dataset.length;
                if (amount) {
                    $statusCount.text(amount);
                    $statusAvailable.removeClass(hiddenCls);
                    $statusEmpty.addClass(hiddenCls);
                } else {
                    $statusEmpty.removeClass(hiddenCls);
                    $statusAvailable.addClass(hiddenCls);
                }
            }

            $elt.html($rendering);

            // if the filter is enabled and a value is present, set the focus on the input field
            if (options.filter && options.filterquery) {
                $rendering.find('[name=filter].focused').focus();
            }

            /**
             * @event dataTable#load.dataTable
             * @param {Object} dataset - The data set used to render the table
             */
            $elt.trigger('load.' + ns, [dataset]);
        },

        /**
         * Query next page
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable('next');
         *
         * @param {jQueryElement} $elt - plugin's element
         * @fires dataTable#forward.datatable
         */
        _next: function($elt) {
            var options = $elt.data(dataNs);

            //increase page number
            options.page += 1;

            //rebind options to the elt
            $elt.data(dataNs, options);

            /**
             * @event dataTable#forward.dataTable
             */
            $elt.trigger('forward.' + ns);

            // Call the query
            this._query($elt);
        },

        /**
         * Query the previous page
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable('previous');
         *
         * @param {jQueryElement} $elt - plugin's element
         * @fires dataTable#backward.datatable
         */
        _previous: function($elt) {
            var options = $elt.data(dataNs);
            if(options.page > 1){

                //decrease page number
                options.page -= 1;

                //rebind options to the elt
                $elt.data(dataNs, options);

                /**
                 * @event dataTable#backward.dataTable
                 */
                $elt.trigger('backward.' + ns);

                // Call the query
                this._query($elt);
            }
        },

        /**
         * Query filtered list of items
         *
         * @param {jQueryElement} $elt - plugin's element
         * @param {String} $filter - the filter input
         * @param {String[]} columns - list of columns in which will be done search
         * @fires dataTable#filter.datatable
         * @fires dataTable#sort.datatable
         * @private
         */
        _filter: function _filter($elt, $filter, columns) {
            var options = $elt.data(dataNs);
            var query = $('input', $filter).val();

            //set the filter
            if (!_.isObject(options.filter)) {
                options.filter = {};
            }

            // set correct filter data
            options.filterquery = query;
            options.filtercolumns = (columns && columns.length) ? columns : [];
            options.page = 1;

            //rebind options to the elt
            $elt.data(dataNs, options);

            /**
             * @event dataTable#filter.datatable
             * @param {Object} options - The options list
             */
            $elt.trigger('filter.' + ns, [options]);

            /**
             * @event dataTable#sort.datatable
             * @param {String} query - The filter query
             */
            $elt.trigger('sort.' + ns, [query]);

            // Call the query
            this._query($elt);
        },

        /**
         * Query the previous page
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable('sort', 'firstname', false);
         *
         * @param {jQueryElement} $elt - plugin's element
         * @param {String} sortBy - the model id of the col to sort
         * @param {Boolean} [asc] - sort direction true for asc of deduced
         * @fires dataTable#sort.datatable
         */
        _sort: function($elt, sortBy, asc) {
            /**
             * @event dataTable#sort.dataTable
             * @param {String} column - The name of the column to sort
             * @param {String} direction - The sort direction
             */
            $elt.trigger('sort.' + ns, [sortBy, asc]);

            this._sortOptions($elt, sortBy, asc);
            this._query($elt);
        },

        /**
         * Set the sort options.
         *
         * @param {jQueryElement} $elt - plugin's element
         * @param {String} sortBy - the model id of the col to sort
         * @param {Boolean|String} [asc] - sort direction true for asc of deduced
         * @returns {Object} - returns the options
         * @private
         */
        _sortOptions: function($elt, sortBy, asc) {
            var options = $elt.data(dataNs);

            if (typeof asc !== 'undefined') {
                if ('asc' !== asc && 'desc' !== asc) {
                    asc = (!!asc) ? 'asc' : 'desc';
                }
                options.sortorder = asc;
            } else if (options.sortorder === 'asc' && options.sortby === sortBy) {
                // If I already sort asc this element
                options.sortorder = 'desc';
            } else {
                // If I never sort by this element or
                // I sort by this element & the order was desc
                options.sortorder = 'asc';
            }

            // Change the sorting element anyway.
            options.sortby = sortBy;

            //rebind options to the elt
            $elt.data(dataNs, options);

            return options;
        },

        /**
         * Gets the selected items. Returns an array of identifiers.
         *
         * @param {jQueryElement} $elt - plugin's element
         * @returns {Array} - Returns an array of identifiers.
         */
        _selection: function($elt) {
            var $selected = $elt.find('[data-item-identifier]').has('td.checkboxes input:checked');
            var selection = [];

            $selected.each(function() {
                selection.push($(this).data('item-identifier'));
            });

            return selection;
        }
    };

    Pluginifier.register(ns, dataTable, {
         expose : ['refresh', 'next', 'previous', 'sort', 'filter', 'selection', 'render']
    });
});
