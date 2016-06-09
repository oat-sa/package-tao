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
 *               every occurrence of deprecated helper usage was replaced
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/include/states/states',
    'taoQtiItem/qtiCreator/widgets/static/helpers/widget',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/media',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline'
], function($, Widget, states, helper, toolbarTpl, inlineHelper){
    'use strict';

    var IncludeWidget = Widget.clone();
    /**
     *
     * @param {Object} options
     * @param {String} options.baseUrl
     */
    IncludeWidget.initCreator = function(options){

        var _this = this,
            xinclude = _this.element;

        this.registerStates(states);
        
        Widget.initCreator.call(this);
        
        inlineHelper.togglePlaceholder(this);
        return;
        //check file exists:
        inlineHelper.checkFileExists(this, 'href', options.baseUrl);
        $('#item-editor-scope').on('filedelete.resourcemgr.' + this.element.serial, function(e, src){
            if(_this.getAssetManager().resolve(xinclude.attr('href')) === _this.getAssetManager().resolve(src)){
                xinclude.attr('href', '');
                inlineHelper.togglePlaceholder(_this);
            }
        });
    };

    IncludeWidget.destroy = function(){
        $('#item-editor-scope').off('.' + this.element.serial);
    };

    IncludeWidget.getRequiredOptions = function(){
        return ['baseUrl', 'uri', 'lang', 'mediaManager'];
    };

    IncludeWidget.buildContainer = function(){
        
        helper.buildBlockContainer(this);
        this.$container.css({
            width: this.element.attr('width'),
            height: this.element.attr('height')
        });
        this.$original.removeAttr('data-serial');
        this.$original[0].setAttribute('width', '100%');
        this.$original[0].setAttribute('height', '100%');

        return this;
    };

    IncludeWidget.createToolbar = function(){

        helper.createToolbar(this, toolbarTpl);

        return this;
    };

    return IncludeWidget;
});
