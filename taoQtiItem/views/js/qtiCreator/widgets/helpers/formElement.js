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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiItem/core/Element',
    'util/dom',
    'ui/incrementer',
    'ui/tooltipster',
    'ui/selecter',
    'ui/inplacer',
    'ui/groupvalidator',
    'taoQtiItem/qtiCreator/widgets/helpers/validators',
    'polyfill/placeholders'
], function($, _, __, Element, dom, spinner, tooltip, select2){
    'use strict';

    var formElement = {
        initWidget : function($form){
            spinner($form);
            tooltip($form);
            select2($form);
        },
        /**
         * register attribute change callback functions when associated form elements has been modified
         *
         * @param {Object} $form - the jQuery form element
         * @param {Object} element - a js qti element (see qtiCreator/model)
         * @param {Object} attributes - key value attributeName:callback, e.g. {identifier:function(element, value, attrName){ element.attr(attrName, value); }}
         * @param {Boolean} [options.validateOnInit = false] - define if the validation should be trigger immediately after the callbacks have been set
         * @param {Boolean} [options.invalidate = false] - define if the validation set the valid/invalidate state to the widget of the element
         */
        setChangeCallbacks : function($form, element, attributes, options){

            attributes = attributes || {};
            options = _.defaults(options || {}, {
                validateOnInit : false,
                invalidate : false
            });

            var _callbackCall = function(name, value, $elt){
                var cb = attributes[name];
                if(_.isFunction(cb)){
                    cb.call($elt[0], element, value, name);
                }
            };

            var callback = {
                simple : function(){

                    var $elt = $(this),
                        name = $elt.attr('name');

                    if($elt.is(':checkbox')){
                        _callbackCall(name, $elt.prop('checked'), $elt);
                    }else{
                        _callbackCall(name, $elt.val(), $elt);
                    }

                },
                withValidation : function(e, valid, elt){

                    if(e.namespace === 'group'){

                        var $elt = $(elt),
                            name = $elt.attr('name');

                        _callbackCall(name, $elt.val(), $elt);
                        if(options.invalidate){
                            element.data('widget').isValid(name, valid);
                        }
                    }
                }
            };

            $form.off('.databinding');
            $form.on('change.databinding keyup.databinding', ':checkbox, :radio, select, :text:not([data-validate]), :hidden:not([data-validate])', callback.simple);
            $form.on('keyup.databinding input.databinding propertychange.databinding', 'textarea', callback.simple);

            $form.on('validated.group.databinding', callback.withValidation);

            _.defer(function(){
                $form.groupValidator({
                    validateOnInit : options.validateOnInit,
                    events : ['change', 'blur', {type : 'keyup', length : 0}],
                    callback : _validationCallback
                });
            });

        },
        initTitle : function($form, element){

            var $title = $form.hasClass('qti-title') ? $form : $form.find('.qti-title');

            $title
                .inplacer({
                    target : $('#qti-title')
                })
                .attr('title', __('Edit modal feedback title'))
                .on('change', function(){
                    element.attr('title', $(this).text());
                });
        },
        /**
         * the simplest form of save callback used in setChangeCallbacks()
         * @param {boolean} allowEmpty
         */
        getAttributeChangeCallback : function(allowEmpty){

            return function(element, value, name){
                if(!allowEmpty && value === ''){
                    element.removeAttr(name);
                }else{
                    element.attr(name, value);
                }
            };
        },
        /**
         * Create a coupled callbacks for min and max value change, when both are using the ui/incrementer widget.
         * It is used to update constraints on one when modifying the other.
         * 
         * @param {Object} $form - the JQuery object representing the form container
         * @param {String} attributeNameMin
         * @param {String} attributeNameMax
         * @param {Object} options
         * @returns {Object}
         */
        getMinMaxAttributeCallbacks : function($form, attributeNameMin, attributeNameMax, options){

            var _defaults = {
                allowNull : false,
                updateCardinality : true,
                attrMethodNames : {
                    remove : 'removeAttr',
                    set : 'attr'
                },
                callback : _.noop
            },
            $max = $form.find('input[name=' + attributeNameMax + ']'),
                callbacks = {};

            //prepare options object
            options = _.defaults(options || {}, _defaults);

            callbacks[attributeNameMin] = function(element, value, name){

                var newOptions = {min : 0};

                value = parseInt(value);
                var isActualNumber = !isNaN(value);

                if(!options.allowNull && (value === 0 || !isActualNumber)){

                    //if a null attribute is not allowed, remove it !
                    element[options.attrMethodNames.remove](name);

                }else if(isActualNumber){

                    //if the value is an actual number
                    element[options.attrMethodNames.set](name, value);
                    newOptions.min = value;

                    var max = parseInt($max.val());

                    if(max < value && !(max === 0 && $max.data('zero') === true)){
                        $max.val(value);
                    }
                }
                //set incrementer min value for maxChoices and trigger keyup event to launch validation
                $max.incrementer('options', newOptions).keyup();

                options.callback(element, value, name);
            };

            callbacks[attributeNameMax] = function(element, value, name){

                value = parseInt(value) || 0;

                if(element.is('interaction')){
                    //update response
                    _updateResponseDeclaration(element, value, options.updateCardinality);
                }

                if(!value && (element.is('orderInteraction') || element.is('graphicOrderInteraction'))){
                    element[options.attrMethodNames.remove](name);//to be removed for order interactions
                }else{
                    element[options.attrMethodNames.set](name, value);//required
                }

                options.callback(element, value, name);
            };

            return callbacks;
        }
    };

    var _updateResponseDeclaration = function(interaction, maxChoice, updateCardinality){

        if(Element.isA(interaction, 'interaction')){
            updateCardinality = (updateCardinality === undefined) ? true : !!updateCardinality;

            var responseDeclaration = interaction.getResponseDeclaration();
            if(updateCardinality){
                responseDeclaration.attr('cardinality', (maxChoice === 1) ? 'single' : 'multiple');
            }

            if(maxChoice){
                //always update the correct response then:
                var correct = [];
                _.each(responseDeclaration.getCorrect(), function(c){
                    if(correct.length < maxChoice){
                        correct.push(c);
                    }else{
                        return false;
                    }
                });
                responseDeclaration.setCorrect(correct);
            }

        }else{
            throw new Error('the first argument must be an interaction, the current element is ' + interaction.qtiClass);
        }


    };

    var _validationCallback = function _validationCallback(valid, results){

        var $input = $(this),
            rule;

        if(dom.contains($input)){

            _createTooltip($input);

            $input.tooltipster('hide');

            if(!valid){

                //invalid input!
                rule = _.where(results, {type : 'failure'})[0];
                if(rule && rule.data.message){
                    $input.tooltipster('content', rule.data.message);
                    if(!$('#mediaManager').children('.opened').length){
                        //only show it when the file manager is hidden
                        $input.tooltipster('show');
                    }
                }

            }

        }



    };

    var _createTooltip = function($input){
        if(!$input.hasClass('tooltipstered')){
            $input.tooltipster({
                theme : 'tao-error-tooltip',
                content : '',
                delay : 350,
                trigger : 'custom'
            });
        }
    };

    return formElement;
});
