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
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer',
], function($, _, __, CommonRenderer){
    'use strict';

    var ResponseWidget = {
        create : function(widget, callback){

            var self = this;
            var interaction = widget.element;
            var $placeholder = $('<select>');
            var $responseWidget = widget.$container.find('.widget-response').append($placeholder);

            //the common renderer is tweaked to render the response selection
            this.commonRenderer = new CommonRenderer({
                shuffleChoices : false,
                themes : false
            });

            this.commonRenderer.load(function(){

                interaction.render({}, $placeholder, '', this);
                interaction.postRender({
                    allowEmpty : false,
                    placeholderText : __('select correct choice')
                }, '', this);

                callback.call(self, this);

                $responseWidget.siblings('.padding').width($responseWidget.width() + 50);//plus icons width

            }, ['inlineChoice', 'inlineChoiceInteraction']);

        },
        setResponse : function(widget, response){
            this.commonRenderer.setResponse(widget.element, this.formatResponse(response));
        },
        destroy : function(widget){
            widget.$container.find('.widget-response').empty();
            widget.$container.find('.padding').removeAttr('style');
        },
        formatResponse : function(response){
            if(!_.isString(response)){
                response = _.values(response);
                if(response && response.length){
                    response = response[0];
                }
            }
            return {base : {identifier : response}};
        },
        unformatResponse : function(formatedResponse){

            var response = [];

            if(formatedResponse.base && formatedResponse.base.identifier){
                response.push(formatedResponse.base.identifier);
            }
            return response;
        }
    };

    return ResponseWidget;
});
