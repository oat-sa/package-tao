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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
define(['jquery'], function($){
    'use strict';
    
    var $toolbarContainer = $('#main-menu > .right-menu');

    /**
     * The TaoToolbar component bind the data-action attribute to controllers. 
     * 
     * @exports tao/controller/main/toolbar
     */
    var taoToolbar = {
        
        /**
         * Set up the toolbar
         */
        setUp : function(){
            $toolbarContainer.find('[data-action]').click(function(){
                
                var $elt = $(this);
                var action = $elt.data('action');
                require([action], function(controller){
                    if(controller &&  typeof controller.start === 'function'){
                        controller.start();
                    }
                });
            });
        }
    };
    
    return taoToolbar;
});
