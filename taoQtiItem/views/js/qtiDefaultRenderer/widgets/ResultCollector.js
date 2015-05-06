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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */
/**
 * TAO QTI API
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 * @requires jquery {@link http://www.jquery.com}
 */
/**
 * The QTIResultCollector class collects the user results of a QTI widgets
 * and return a well formated variable
 * <i>(the type of the returned variable is deterministic for the result processing)</i>
 *
 * @class QTIResultCollector
 * @property {Object} options the widget parameters
 */
define(function(){
    
    var ResultCollector = function(interaction){

        //keep the current instance pointer
        var _this = this;

        /**
         * The widget options
         * @fieldOf QTIResultCollector
         * @type Object
         */
        this.opts = interaction.getAttributes();

        /**
         * The id of the widget
         * @fieldOf QTIResultCollector
         * @type String
         */
        this.id = interaction.attr('identifier');

        /**
         * The collected responses
         * @fieldOf QTIResultCollector
         * @type Array
         */
        this.responses = [];

        /**
         * Append a response record to responses stack
         * @fieldOf QTIResultCollector
         * @type Mixed
         */
        this.append = function(data){
            this.responses.push(data);
            return this.responses.length - 1;
        }

        /**
         * Delete a response record from responses stack
         * @fieldOf QTIResultCollector
         * @type Integer
         */
        this.remove = function(responseId){
            delete this.responses[responseId];
        }

        this.get = function(responseId){
            return this.responses[responseId];
        }

        /**
         * Completely replace the response
         * 
         * @param {type} response
         */
        this.set = function(response){
            this.responses = response;
        }

        this.isSet = function(value){
            var ret = -1;
            for(var i in this.responses){
                if(typeof(value) === 'object' || typeof(value) === 'array'){
                    ret = i;
                    for(var k in value){
                        if(value[k] != this.responses[i][k]){
                            ret = -1;
                            break;
                        }
                    }
                }else{
                    if(value == this.responses[i]){
                        ret = i;
                        break;
                    }
                }
            }
            return ret;
        }

        /**
         * Collect the results of a <b>choice</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.choice = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : (_this.opts["maxChoices"] != 1) ? [] : null
            };

            $("#" + _this.id + " .tabActive").each(function(){
                if(_this.opts["maxChoices"] != 1)
                    result.value.push(this.id);
                else
                    result.value = this.id;
            });

            return result;
        };


        /**
         * Collect the results of an <b>order</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.order = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : new Object()
            };
            var i = 0;
            var listClass = (_this.opts['orientation'] == 'horizontal') ? 'qti_choice_list_horizontal' : 'qti_choice_list';
            $('#' + _this.id + ' ul.' + listClass + ' li').each(function(){
                result.value[i] = this.id;
                i++;
            });
            return result;
        };


        /**
         * Collect the results of an <b>associate</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.associate = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : (_this.opts["maxAssociations"] == 1) ? null : []
            };

            $("#" + _this.id + " .qti_association_pair").each(function(){
                // The field has not been filled
                if(!$(this).find('li:first').find('.filled_pair').length){
                    return;
                }

                // Get the associated identifier
                var firstId = '';
                if($(this).find('li:first').find('.filled_pair').length > 0){
                    firstId = $(this).find('li:first').find('.filled_pair').attr('id').replace('pair_', '');
                }
                var lastId = '';
                if($(this).find('li:last').find('.filled_pair').length > 0){
                    lastId = $(this).find('li:last').find('.filled_pair').attr('id').replace('pair_', '');
                }

                // create the element following the matching format, which is always a base-type "pair"
                var elt = [firstId, lastId];

                //maxAssociations = 0 => infinite association available
                if(_this.opts["maxAssociations"] == 1){
                    result.value = elt;
                }else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
                    result.value.push(elt);
                }

            });

            return result;
        };


        /**
         * Collect the results of text based widget :
         * <b>text_entry</b> and <b>extended_text</b>
         * @methodOf QTIResultCollector
         * @todo Multiple not tested
         * @returns {Object} the results
         */
        this.text = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : null
            };

            //single mode
            if($('#' + this.id).prop('tagName').toLowerCase() !== 'div'){
                switch(_this.opts['baseType']){
                    case "integer":
                        result.value = parseInt($("#" + _this.id).val());
                        break;
                    case "float":
                        result.value = parseFloat($("#" + _this.id).val());
                        break;
                    case "string":
                    default:
                        result.value = $("#" + _this.id).val();
                }
            }
            //multiple mode
            else{
                result.value = new Array();
                $("#" + _this.id + " :text").each(function(){
                    switch(_this.opts['baseType']){
                        case "integer":
                            result.value.push(parseInt($(this).val()));
                            break;
                        case "float":
                            result.value.push(parseFloat($(this).val()));
                            break;
                        case "string":
                        default:
                            result.value.push($(this).val());
                    }
                });
            }

            return result;
        };


        /**
         * @methodOf QTIResultCollector
         * @see QTIResultCollector#text
         */
        this.text_entry = this.text;


        /**
         * @methodOf QTIResultCollector
         * @see QTIResultCollector#text
         */
        this.extended_text = this.text;


        /**
         * Collect the results of an <b>inline_choice</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.inline_choice = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : $("#" + _this.id).val()
            };
            return result;
        };


        /**
         * Collect the results of an <b>hottext</b> widget
         * @methodOf QTIResultCollector
         * @returnss {Object} the results
         */
        this.hottext = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : (_this.opts["maxChoices"] != 1) ? [] : null
            };
            $("#" + _this.id + " .hottext_choice_on").each(function(){
                if(_this.opts["maxChoices"] != 1){
                    result.value.push(this.id.replace(/^hottext_choice_/, ''));
                }else{
                    result.value = this.id.replace(/^hottext_choice_/, '');
                }

            });
            return result;
        };


        /**
         * Collect the results of an <b>gap_match</b> widget
         * @methodOf QTIResultCollector
         * @todo does not work with single cardinality
         * @returns {Object} the results
         */
        this.gap_match = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : []
            };

            $("#" + _this.id + " .filled_gap").each(function(){
                var choiceId = $(this).attr('id').replace('gap_', '');
                var groupId = $(this).parent().attr('id');
                result.value.push({0 : groupId, 1 : choiceId});
            });

            return result;
        };


        /**
         * Collect the results of a <b>match</b> widget
         * @methodOf QTIResultCollector
         * @todo does not work with single cardinality
         * @returns {Object} the results
         */
        this.match = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : (_this.opts["maxAssociations"] == 1) ? null : []
            };

            $("#" + _this.id + " .tabActive").each(function(){
                var subset = new Object();
                var classes = $(this).attr('class').split(' ');
                if(classes.length > 0){
                    var i = 0;
                    while(i < classes.length){
                        if(/^xnode_/.test(classes[i])){
                            subset[0] = classes[i].replace('xnode_', '');
                        }
                        if(/^ynode_/.test(classes[i])){
                            subset[1] = classes[i].replace('ynode_', '');
                        }
                        i++;
                    }

                    //maxAssociations = 0 => infinite association available
                    if(_this.opts["maxAssociations"] == 1){
                        result.value = subset;
                    }else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
                        result.value.push(subset);
                    }

                }
            });
            return result;
        };

        /**
         * Collect the results of an <b>hotspot</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.hotspot = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : []
            };

            for(var i in this.responses){
                if(this.opts.maxChoices == 1){
                    result.value = this.responses[i];
                }else{
                    result.value.push(this.responses[i]);
                }
            }

            return result;
        };

        /**
         * Collect the results of an <b>hotspot</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.select_point = function(){

            var result = {
                "identifier" : this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : []
            };

            for(var i in this.responses){
                var r = this.responses[i];
                if(!isNaN(r.x) && !isNaN(r.y)){
                    result.value.push({
                        '0' : r.x,
                        '1' : r.y
                    });
                }
            }

            return result;
        };

        /**
         * Collect the results of a <b>graphic order</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.graphic_order = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : []
            };

            for(var i in this.responses){
                result.value.push(this.responses[i]);
            }
            return result;
        };

        /**
         * Collect the results of a <b>graphic associate</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.graphic_associate = function(){
            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : (_this.opts["maxAssociations"] == 1) ? null : []
            };
            var pairs = $("#" + _this.id).data('pairs');
            for(var i in pairs){
                if(typeof pairs[i] == 'string'){
                    var pair = pairs[i].split(' ');
                    if(pair.length == 2){
                        if(_this.opts["maxAssociations"] == 1){
                            result.value = pair;
                        }else if(_this.opts["maxAssociations"] == 0 || result.value.length < _this.opts["maxAssociations"]){
                            result.value.push(pair);
                        }
                    }
                }
            }
            return result;
        };

        /**
         * Collect the results of a <b>graphic gap match</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.graphic_gap_match = function(){

            var result = {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : []
            };

            for(var i in this.responses){
                var r = this.responses[i];
                if(r.hotspot && r.gapImg){
                    result.value.push({0 : r.hotspot, 1 : r.gapImg});//directedPair: {associableHotspot, gapImg}
                }
            }

            return result;
        };

        /**
         * Collect the results of an <b>slider</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.slider = function(){
            return {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : parseInt($("#" + _this.id + '_qti_slider_value').val())
            };
        };

        /**
         * Collect the results of an <b>upload</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.upload = function(){
            var value = 0;
            switch(_this.opts['baseType']){
                case "float":
                    value = parseFloat($("#" + _this.id + '_data').val());
                    break;
                case "integer":
                default:
                    value = parseInt($("#" + _this.id + '_data').val());
            }

            return {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : value
            };
        };

        /**
         * Collect the results of an <b>endattempt</b> widget
         * @methodOf QTIResultCollector
         * @returns {Object} the results
         */
        this.end_attempt = function(){
            var value = 0;
            if(parseInt($("#" + _this.id + '_data').val()) > 1){
                value = 1;
            }

            return {
                "identifier" : _this.opts['responseIdentifier'] // Identifier of the response
                    , "value" : value
            };
        };
    };

    return ResultCollector;
});

