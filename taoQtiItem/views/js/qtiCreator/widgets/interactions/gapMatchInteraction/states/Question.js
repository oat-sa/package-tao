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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/containerInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/gapMatch',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/gap-create',
    'lodash'
], function(stateFactory, Question, formElement, formTpl, gapTpl, _){
    'use strict';

    var GapMatchInteractionStateQuestion = stateFactory.extend(Question, function(){

        //ensure that the cardinality of the interaction response is consistent with thte number of gaps
        this.syncCardinality();
        this.preventSingleChoiceDeletion();

    }, function(){

        this.widget.offEvents('question');
    });

    GapMatchInteractionStateQuestion.prototype.syncCardinality = function(){

        var interaction = this.widget.element,
            response = interaction.getResponseDeclaration();

        var updateCardinality = function(data){

            var cardinality,
                choice = data.element || data.choice;

            if(choice.qtiClass === 'gap'){
                cardinality = _.size(interaction.getGaps()) === 1 ? 'single' : 'multiple';
                response.attr('cardinality', cardinality);
            }
        };

        this.widget
            .on('elementCreated', updateCardinality)
            .on('deleted', updateCardinality);
    };

    GapMatchInteractionStateQuestion.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            shuffle : !!interaction.attr('shuffle')
        }));

        formElement.initWidget($form);

        formElement.setChangeCallbacks($form, interaction, {
            shuffle : formElement.getAttributeChangeCallback()
        });

    };

    GapMatchInteractionStateQuestion.prototype.getGapModel = function(){

        return {
            toolbarTpl : gapTpl,
            qtiClass : 'gap',
            afterCreate : function afterCreate(interactionWidget, newGapWidget, text){
                var choice,
                    choiceWidget;

                //after the gap is created, delete it
                choice = interactionWidget.element.createChoice(text);
                interactionWidget.$container.find('.choice-area .add-option').before(choice.render());
                choice.postRender();
                choiceWidget = choice.data('widget');
                choiceWidget.changeState('question');
                newGapWidget.changeState('choice');
            }
        };
    };

    GapMatchInteractionStateQuestion.prototype.preventSingleChoiceDeletion = function(){

        var interaction = this.widget.element,
            $container = this.widget.$container;

        var _toggleDeleteButtonVisibility = function(){

            var choiceCount = 0,
                $deleteButtons = $container.find('.choice-area .qti-choice [data-role=delete]');

            _.each(interaction.getChoices(), function(choice){
                if(!choice.data('deleting')){
                    choiceCount++;
                }
            });

            if(choiceCount <= 1){
                $deleteButtons.hide();
            }else{
                $deleteButtons.show();
            }
        };

        _toggleDeleteButtonVisibility();

        this.widget
            .on('deleted', _toggleDeleteButtonVisibility)
            .on('choiceCreated', _toggleDeleteButtonVisibility);

        this.widget.afterStateInit(function(e, element, state){
            if(state.name === 'deleting' && element.is('gapText')){
                _toggleDeleteButtonVisibility();
            }
        }, 'question');

        this.widget.afterStateExit(function(e, element, state){
            if(state.name === 'deleting' && element.is('gapText')){
                _toggleDeleteButtonVisibility();
            }
        }, 'question');

    };

    return GapMatchInteractionStateQuestion;
});
