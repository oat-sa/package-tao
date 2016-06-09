
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
 * Copyright (c) 2014 (update and modification) Open Assessment Technologies SA;
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'layout/section',
    'ui/feedback',
    'ui/datatable'
],
function($, _, __, section, feedback, datatable){
    'use strict';

    /**
     * Create the table that contains the search results
     * @param {Object} data - the datatable parameters
     * @param {Object} data.model - the datatable model
     * @param {Object} data.url
     * @param {Object} data.params - extra parameters to give to the datatable endpoint
     * @param {Object} data.filters - extra parameters to give to the datatable endpoint
     */
    var buildResponseTable  = function buildResponseTable(data){

        //update the section container
        var $tableContainer = $('<div class="flex-container-full"></div>');
        section.updateContentBlock($tableContainer);

        //create a datatable
        $tableContainer.datatable({
                'url': data.url,
                'model' : _.values(data.model),
                'actions' : {
                   'open' : function openResource(id){
                            $('.tree').trigger('refresh.taotree', [{loadNode : id}]);
                    } 
                },
                'params' : {
                    'params' : data.params,
                    'filters': data.filters,
                    'rows': 20
                 }
        });
    };

    /**
     * Behavior of the tao backend global search.
     * It runs by himself using the init method.
     * 
     * @example  search.init();
     *
     * @exports layout/search
     */
    var searchComponent =  {

        /**
         * Initialize, only entry point
         */
        init : function init(){

            var $container = $('.action-bar .search-area');
            var $searchInput = $('input' , $container);
            var $searchBtn = $('button' , $container);
  
            if($container && $container.length){
     
                //throttle and control to prevent sending too many requests
                var running = false;
                var searchHandler = _.throttle(function searchHandler(query){ 
                    if(running === false){
                        running = true;
                        $.ajax({
                            url : $container.data('url'),
                            type : 'POST',
                            data :  {query : query},
                            dataType : 'json'
                        }).done(function(response){
                            if(response && response.result && response.result === true){
                                buildResponseTable(response);
                            } else {
                                feedback().warning(__('No results found'));
                            }
                        }).complete(function(){
                            running = false;
                        }); 
                    }
                }, 100);

                //clicking the button trigger the request
                $searchBtn.off('click').on('click', function(e){
                    e.preventDefault();
                    searchHandler($searchInput.val());
                });

                //or press ENTER
                $searchInput.off('keypress').on('keypress', function(e){
                    var query = $searchInput.val();
                    if(e.which === 13){
                        e.preventDefault();
                        searchHandler(query);
                    }
                });
            }
        }
    };

    return searchComponent;
});
