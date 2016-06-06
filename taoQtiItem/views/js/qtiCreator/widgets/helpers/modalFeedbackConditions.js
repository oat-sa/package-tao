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
 * Copyright (c) 2016 Open Assessment Technologies SA;
 *
 */
define([
    'lodash',
    'i18n',
    'jquery',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/formElement',
    'taoQtiItem/qtiCreator/editor/response/choiceSelector'
], function(_, __, $, formElement, choiceSelector){
    
    'use strict';
    
    var _ns = '.modal-feedback-condition';
    
    var _availableConditions = [
        {
            name : 'correct',
            label : __('correct'),
            init : hideScore,
            onSet : onSetScore,
            onUnset : onUnsetCorrect
        },
        {
            name : 'incorrect',
            label : __('incorrect'),
            init : hideScore,
            onSet : onSetScore,
            onUnset : onUnsetCorrect
        },
        {
            name : 'choices',
            label : __('choices'),
            init : function initChoice(fbRule, $select){
                hideScore(fbRule, $select);
                var condition = this.name;
                var response = fbRule.comparedOutcome;
                var interaction = response.getInteraction();
                var $choiceSelectorContainer = $('<div>', {'class' : 'choiceSelectorContainer'}).insertAfter($select);

                var cSelector = choiceSelector({
                    renderTo : $choiceSelectorContainer,
                    interaction : interaction,
                    choices : fbRule.comparedValue || [],
                    titleLength : 30
                });
                $choiceSelectorContainer.data('choice-selector', cSelector);

                cSelector.on('change', function(selectedChoices){
                    response.setCondition(fbRule, condition, selectedChoices || []);
                });

            },
            onSet : function onSetChoices(fbRule, $select){
                var response = fbRule.comparedOutcome;
                var interaction = response.getInteraction();
                var choice;
                var intialValue = [];
                if(!response.isCardinality(['multiple', 'ordered'])){
                    choice = _.head(_.values(interaction.getChoices()));
                    if(choice){
                        intialValue = [choice];//a single cardinality response comparison requires a choice selected
                    }
                }
                fbRule.comparedOutcome.setCondition(fbRule, this.name, intialValue);
            },
            onUnset : function onUnsetChoices(fbRule, $select){
                //this needs to be executed to restore the feedback rule value
                _resetScore(fbRule, $select);
                this.destroy($select);
            },
            destroy : function($select){
                var $cContainer = $select.next('.choiceSelectorContainer');
                var choiceSelector = $cContainer.data('choice-selector');
                if(choiceSelector){
                    choiceSelector.destroy();
                    $cContainer.remove();
                }
            },
            filter : function filterChoices(response){
                var interaction = response.getInteraction();
                return (interaction.is('choiceInteraction') || interaction.is('inlineChoiceInteraction'));
            }
        },
        {
            name : 'lt',
            label : '<',
            init : initCompare,
            onSet : onSetScore
        },
        {
            name : 'lte',
            label : '<=',
            init : initCompare,
            onSet : onSetScore
        },
        {
            name : 'equal',
            label : '=',
            init : initCompare,
            onSet : onSetScore
        },
        {
            name : 'gte',
            label : '>=',
            init : initCompare,
            onSet : onSetScore
        },
        {
            name : 'gt',
            label : '>',
            init : initCompare,
            onSet : onSetScore
        }
    ];
    
    function _resetScore(fbRule, $select){
        $select.siblings('.feedbackRule-compared-value').val('0');
    }

    function onSetScore(fbRule, $select){
        var response = fbRule.comparedOutcome;
        var condition = this.name;
        var $comparedValue = $select.siblings('.feedbackRule-compared-value');
        formElement.setScore($comparedValue, {
            required : true,
            set : function(key, value){
                response.setCondition(fbRule, condition, value);
            }
        });
    }

    function onUnsetCorrect(fbRule, $select){
        _resetScore(fbRule, $select);
    }

    function initCompare(fbRule, $select){

        var response = fbRule.comparedOutcome;
        $select.siblings('.feedbackRule-compared-value')
            .show()
            .off('keyup' + _ns)
            .on('keyup' + _ns, '.feedbackRule-compared-value', function(){

                var fbRule = response.getFeedbackRule($(this).parents('.feedbackRule-container').data('serial'));

                formElement.setScore($(this), {
                    required : true,
                    set : function(key, value){
                        response.setCondition(fbRule, fbRule.condition, value);
                    }
                });
            });
    }

    function hideScore(fbRule, $select){
        $select.siblings('.feedbackRule-compared-value').hide();

    }
    
    function getAvailableConditions(response){

        return _.filter(_availableConditions, function(condition){
            if(_.isFunction(condition.filter)){
                return condition.filter(response);
            }
            return true;//accept by default
        });
    }
    
    return  {
        get : getAvailableConditions
    };
});