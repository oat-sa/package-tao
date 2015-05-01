/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 
    'lodash',
    'i18n', 
    'helpers',
    'module',
    'ui/datatable', 
    'jquery.fileDownload'
], function($, _, __, helpers, module) {
    'use strict';

    /**
     * @exports taoOutcomeUi/controller/resultTable
     */
    var resulTableController =  {

        /**
         * Controller entry point
         */
        start : function(){
	   
           var conf = module.config();
           var $container = $(".result-table");
           var $filterField = $('.result-filter', $container);
           var $tableContainer = $('.result-table-container', $container);
           var filter = conf.filter || 'lastSubmitted';
           var classUri = conf.classUri || '';
            //keep columns through calls
            var columns = [];
            var groups = {};

            /**
             * Load columns to rebuild the datatable dynamically
             * @param {String} url  - the URL that retrieve the columns
             * @param {String} [action = 'add'] - 'add' or 'remove' the retrieved columns
             * @param {Function} done - once the datatable is loaded
             */
            var buildGrid = function buildGrid(url, action, done){
                $.ajax({
                    url : url,
                    dataType : 'json',
                    data : {filter : filter, classUri : classUri},
                    type :'GET'
                }).done(function(response){
                    if(response && response.columns){
                        if(action === 'remove'){
                            columns = _.reject(columns, function(col){
                               return _.find(response.columns, function(resCol){
                                    return _.isEqual(col, resCol);
                               });
                            });
                        } else {
                            if(response.first !== undefined && response.first === true){
                                columns = response.columns.concat(columns);
                            }
                            else{
                                columns = columns.concat(response.columns);
                            }
                        }
                        _buildTable(done);
                    }
                });
            };
            
            /**
             * Rebuild the datatable 
             * @param {Function} done - once the datatable is loaded
             */
            var _buildTable = function _buildTable(done){
                var model = [];

                //set up model from columns 
                _.forEach(columns, function(col){
                    model.push({
                        id : col.prop || (col.contextId + '_' + col.variableIdentifier),
                        label : col.label,
                        sortable: false
                    });
                });
                
                //re buid the datatable
                $tableContainer
                    .empty()
                    .data('ui.datatable', null)
                    .off('load.datatable')
                    .on('load.datatable', function(){

                        //enable to export the loaded table
                        $('.result-export', $container)
                            .off('click')
                            .removeClass('disabled')
                            .on('click', function(e){
                                e.preventDefault();
                                $.fileDownload(helpers._url('getCsvFile', 'ResultTable', 'taoOutcomeUi'), {
                                    preparingMessageHtml: __("We are preparing your report, please wait..."),
                                    failMessageHtml: __("There was a problem generating your report, please try again."),
                                    httpMethod: 'POST',
                                    data: {'filter': filter, 'columns': columns, classUri : classUri}
                                });
                            });

                        if(_.isFunction(done)){
                            done();
                            done = '';
                        }
                    })
                    .datatable({
                        url : helpers._url('data', 'ResultTable', 'taoOutcomeUi', {filterData : filter}),
                        querytype : 'POST',
                        params : {columns : columns,  '_search' : false, classUri : classUri},
                        model :  model
                    });
            };

            //group button to toggle them
            $('[data-group]', $container).each(function(){
                var $elt = $(this);
                var group = $elt.data('group');
                var action = $elt.data('action');
                groups[group] = groups[group] || {};
                groups[group][action] = $elt;
            });

            //regarding button data, we rebuild the table
            $container.on('click', '[data-group]', function(e){
                e.preventDefault();
                var $elt    = $(this);
                var group   = $elt.data('group');
                var action  = $elt.data('action');
                var url     = $elt.data('url');
                buildGrid(url, action, function(){
                    _.forEach(groups[group], function($btn){
                       $btn.toggleClass('hidden');
                    });
                });
            });

            //default table
            buildGrid(helpers._url('getResultOfSubjectColumn', 'ResultTable', 'taoOutcomeUi', {filter : filter}));

            //setup the filtering
            $filterField.select2({
                minimumResultsForSearch : -1
            }).select2('val', filter);

            $('.result-filter-btn', $container).click(function(e) {
                filter = $filterField.select2('val');
                //rebuild the current table
                _buildTable();
            });
        }
    };
    return resulTableController;
});
