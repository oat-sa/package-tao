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
        'start'   : 0,
        'rows': 25,
        'page': 1,
        'sortby': 'id',
        'sortorder': 'asc',
        'model'   : null,
        'actions' : null
    };

    var actionHeader = {
        id : null,
        label : __('Actions'),
        sortable : false
    };

    /**
     * The dataTable component makes you able to browse itemss and bind specific
     * actions to undertake for edition and removal of them.
     *
     * @exports ui/datatable
     */
    var dataTable = {

        /**
         * Initialize the plugin.
         *
         * Called the jQuery way once registered by the Pluginifier.
         * @example $('selector').datatable({});
         *
         * @constructor
         * @param {Object} options - the plugin options
         * @param {String} options.url - the URL of the service used to retrieve the resources.
         * @param {Function} options.actions.xxx - the callback function for items xxx, with a single parameter representing the identifier of the items.
         * @fires dataTable#create.datatable
         * @returns {jQueryElement} for chaining
         */
        init: function(options) {

            var self = dataTable;
            options = _.defaults(options, defaults);

            return this.each(function() {
                var $elt = $(this);
                
                if(!$elt.data(dataNs)){
               
                    //add data to the element
                    $elt.data(dataNs, options);

                    $elt.one('load.' + ns , function(){
                        /**
                         * @event dataTable#create.datatable
                         */ 
                        $elt.trigger('create.' + ns);
                    }); 

                    self._query($elt);
                } else {
                    self._refresh($elt);
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
        */
        _refresh : function($elt){
            this._query($elt); 
        },

        /**
         * Query the server for data and load the table.
         *
         * @private
         * @param {jQueryElement} $elt - plugin's element 
         * @fires dataTable#load.datatable
         */
        _query: function($elt){
            var self = this;
            var options = $elt.data(dataNs);
            var parameters = _.merge({},_.pick(options, ['rows', 'page', 'sortby', 'sortorder']), options.params || {});

            $.ajax({
                url: options.url,
                data: parameters,
                dataType : 'json',
                type: options.querytype || 'GET'
            }).done(function(response) {

                var rows = {};

                // Add the list of custom actions to the response for the tpl
                if(options.actions){
                    response.actions = _.keys(options.actions);
                }

                // Add the column into the model
                if (options.actions !== null && _.last(options.model).label !== actionHeader.label) {
                    options.model.push(actionHeader);
                }

                // Add the model to the response for the tpl
                response.model = options.model;

                // Call the rendering
                var $rendering = $(layout(response));

                // the readonly property contains an associative array where keys are the ids of the items (lines)
                // the value can be a boolean (true for disable buttons, false to enable)
                // it can also bo an array that let you disable/enable the action you want
                // readonly = {
                //  id1 : {'view':true, 'delete':false},
                //  id2 : true
                //}
                _.forEach(response.readonly, function(values, id){
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
                _.forEach(options.actions, function(action,name){
                    
                    $rendering
                        .off('click','.'+name)
                        .on('click','.'+name, function(e){
                            e.preventDefault();
                            var $elt = $(this);
                            if(!$elt.hasClass('disabled')){
                                action.apply($elt,[$elt.parent().data('item-identifier')]);
                            }
                        });
                });

                // Now $rendering takes the place of $elt...
                var $forwardBtn = $rendering.find('.datatable-forward');
                var $backwardBtn = $rendering.find('.datatable-backward');
                var $sortBy = $rendering.find('th[data-sort-by]');
                var $sortElement = $rendering.find('[data-sort-by="'+ options.sortby +'"]');

                $forwardBtn.click(function() {
                    self._next($elt);
                });

                $backwardBtn.click(function() {
                    self._previous($elt);
                });

                $sortBy.click(function() {
                    self._sort($elt, $(this).data('sort-by'));
                });

                // Remove sorted class from all th
                $('th.sorted',$rendering).removeClass('sorted');
                // Add the sorted class to the sorted element and the order class
                $sortElement.addClass('sorted').addClass('sorted_'+options.sortorder);

                if (parameters.page === 1) {
                    $backwardBtn.attr('disabled', '');
                } else {
                    $backwardBtn.removeAttr('disabled');
                }

                if (response.page >= response.total) {
                    $forwardBtn.attr('disabled', '');
                } else {
                    $forwardBtn.removeAttr('disabled');
                }


                $elt.html($rendering);

                /**
                 * @event dataTable#load.dataTable
                 */ 
                $elt.trigger('load.datatable');
            });
        },

       /**
        * Query next page 
        * 
        * Called the jQuery way once registered by the Pluginifier.
        * @example $('selector').datatable('next');
        *
        * @param {jQueryElement} $elt - plugin's element 
        */
        _next: function($elt) {
            var options = $elt.data(dataNs);

            //increase page number
            options.page += 1;
            
            //rebind options to the elt
            $elt.data(dataNs, options);

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
        */
        _previous: function($elt) {
            var options = $elt.data(dataNs);
            if(options.page > 1){
 
                //decrease page number
                options.page -= 1;
                
                //rebind options to the elt
                $elt.data(dataNs, options);

                // Call the query
                this._query($elt);
            }
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
        */
        _sort: function($elt, sortBy, asc) {
            var options = $elt.data(dataNs);
        
            if(typeof asc !== 'undefined'){
                options.sortorder = (!!asc) ? 'asc' : 'desc';
            } else if (options.sortorder === 'asc' && options.sortby === sortBy) {
                    // If I already sort asc this element
                    options.sortorder = 'desc';
                }else{
                    // If I never sort by this element or
                    // I sort by this element & the order was desc
                    options.sortorder = 'asc';
                }

            // Change the sorting element anyway.
            options.sortby = sortBy;

            //rebind options to the elt
            $elt.data(dataNs, options);

            // Call the query
            this._query($elt);
        }
    };

    Pluginifier.register(ns, dataTable, {
         expose : ['refresh', 'next', 'previous', 'sort']
    });
});
