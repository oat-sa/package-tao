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
define(['IMSGlobal/jquery_2_1_1', 'OAT/lodash'], function($, _){
    
    'use strict';
    
    /**
     * Get all assets found in the $container and returns an object containing [assetId => assetUrl]
     * 
     * @param {jQuery} $container
     * @returns {object}
     */
    function getAllAssets($container){
        var assets = {};
        var $assets = $($container.find('[type="text/x-asset-manifest"]').html());
        $assets.each(function(){
            
            var $asset = $(this),
                id = $asset.data('asset-id'),
                src = $asset.attr('src');
                
            if(id && src){
                assets[id] = src;
            }
        });
        return assets;
    }
    
    /**
     * Create an asset manager object from a JQuery container
     * 
     * @param {jQuery} $container
     * @returns {object}
     */
    return function asset($container){
        
        var assets = getAllAssets($container);
        
        return {
            exists : function exists(id){
                return (id && assets[id]);
            },
            get : function get(id){
                return assets[id] || '';
            },
            getAll : function(){
                return _.clone(assets);
            }
        };
    };
});