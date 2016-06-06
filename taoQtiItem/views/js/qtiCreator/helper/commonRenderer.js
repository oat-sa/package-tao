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
define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
    'taoQtiItem/qtiCommonRenderer/helpers/container'
], function(_, Renderer, containerHelper){
    "use strict";

    //store the curret execution context of the common renderer (preview)
    var _$previousContext = null;

    //configure and instanciate once only:
    var _renderer = new Renderer({
        shuffleChoices : true
    });

    var commonRenderer = {
        render : function(item, $container){

            commonRenderer.setContext($container);

            return _renderer.load(function(){

                $container.append(item.render(this));
                item.postRender({}, '', this);

            }, item.getUsedClasses());
        },
        get : function(){
            return _renderer;
        },
        setOption : function(name, value){
            return _renderer.setOption(name, value);
        },
        setOptions : function(options){
            return _renderer.setOptions(options);
        },
        setContext : function($context){
            _$previousContext = $context;
            return containerHelper.setContext($context);
        },
        restoreContext : function(){
            containerHelper.setContext(_$previousContext);
             _$previousContext = null;
        },
        load : function(qtiClasses, done){
            return _renderer.load(function(){
                if(_.isFunction(done)){
                    done.apply(this, arguments);
                }
            }, qtiClasses);
        }
    };

    return commonRenderer;

});
