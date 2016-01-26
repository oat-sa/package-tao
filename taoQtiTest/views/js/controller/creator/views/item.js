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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define(['jquery', 'taoQtiTest/controller/creator/templates/index'], function($, templates){
    'use strict';
  
    var itemTemplate = templates.item;
 
   /**
     * The ItemView setup items related components
     * @exports taoQtiTest/controller/creator/views/item
     * @param {Function} loadItems - the function used to get items from the server
     */
   var itemView =  function(loadItems){
            
        var $panel     = $('.test-creator-items .item-selection'); 
        var $search    = $('#item-filter');
        var $itemBox   = $('.item-box', $panel);
        
        if(typeof loadItems === 'function'){
            //search pattern is empty the 1st time, give it undefined
            loadItems(undefined, function(items){
                update(items);
                setUpLiveSearch();
            });
        }
        
        /**
         * Set up the search behavior: once 3 chars are enters into the field,
         * we load the items that matches the given search pattern.
         * @private
         */
        function setUpLiveSearch (){
            var timeout;
            
            var liveSearch = function(){
                var pattern = $search.val();
                if(pattern.length > 1 || pattern.length === 0){
                    clearTimeout(timeout);
                    timeout = setTimeout(function(){
                        loadItems(pattern, function(items){
                            update(items);
                        });
                    }, 300);
                }
            };
            
            //trigger the search on keyp and on the magnifer button click
            $search.keyup(liveSearch)
                     .siblings('.ctrl').click(liveSearch);
        }
        
        /**
         * Update the items list
         * @private
         * @param {Array} items - the new items
         */
        function update (items){
            disableSelection();
            $itemBox.empty().append(itemTemplate(items));
            enableSelection();
        }
    
        /**
         * Disable the selectable component
         * @private
         * @param {Array} items - the new items
         */
        function disableSelection (){
            if($panel.data('selectable')){
                $panel.selectable('disable');
            }
        }
    
        /**
         * Enable to select items to be added to sections
         * using the jquery-ui selectable.
         * @private
         */
        function enableSelection (){
            
            if($panel.data('selectable')){
                $panel.selectable('enable');
            } else {
                $panel.selectable({
                    filter: 'li',
                    selected: function( event, ui ) {
                        $(ui.selected).addClass('selected');
                    },
                    unselected: function( event, ui ) {
                        $(ui.unselected).removeClass('selected');
                    },
                    stop: function(){
                        $(this).trigger('itemselect.creator', $('.selected')); 
                    }
                });
            }
        }
   };
    
    return itemView;
});
