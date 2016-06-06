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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA
 *
 */
define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Container'
], function(_, editable, Container){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        afterCreate : function(){
            this.body('<p>Lorem ipsum dolor sit amet, consectetur adipisicing ...</p>');
        },
        beforeRemove : function(){
            _.each(this.getComposingElements(), function(element){
                if(_.isFunction(element.beforeRemove)){
                    element.beforeRemove();
                }
            });
        }
    });
    return Container.extend(methods);
});