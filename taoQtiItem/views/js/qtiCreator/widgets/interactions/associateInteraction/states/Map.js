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
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/ResponseWidget',
    'lodash',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/associableChoice.answer'
], function($, stateFactory, Map, responseWidget, _, responseToolbarTpl){

    'use strict';

    var AssociateInteractionStateCorrect = stateFactory.create(Map, function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //init response widget in responseMapping mode:
        responseWidget.create(_widget, true);

        //start bind events to detect response changes:
        this.bindResponseChangeListener();

        //finally, apply defined correct response and response mapping:
        responseWidget.setResponse(interaction, _.keys(responseWidget.getResponseSummary(response)));

    }, function(){

        this.widget.$container.off('responseChange.qti-widget');

        responseWidget.destroy(this.widget);

        this.widget.$container.find('.mini-tlb').remove();
    });

    AssociateInteractionStateCorrect.prototype.bindResponseChangeListener = function(){

        var _widget = this.widget,
            interaction = _widget.element,
            response = interaction.getResponseDeclaration();

        //@todo to be mapped to actual value
        var _defaultMappingValue = 0;

        var _saveCorrect = function(){
            var correct = [];
            $('input[name="correct_' + _widget.serial + '[]"]:checked').each(function(){
                correct.push($(this).data('pairIdentifier'));
            });
            response.setCorrect(correct);
        };

        _widget.$container.on('responseChange.qti-widget', function(e, data, extraData){

            var $miniToolbar;

            if(extraData.type === 'added'){

                //choice identifier pair:
                var pair = [],
                    pairs = [],
                    $filled = extraData.$pair.children('.filled');

                if($filled.length === 2){

                    pairs = responseWidget.getResponseSummary(response);

                    //the pair is complete:
                    $filled.each(function(){

                        var serial = $(this).data('serial'),
                            choice = interaction.getChoice(serial);

                        pair.push(choice.id());
                    });

                    pair.sort();

                    var pairIdentifier = pair.join(' ');
                    $miniToolbar = extraData.$pair.children('.mini-tlb');

                    if(!$miniToolbar.length){

                        //if does not exist yet, create it:
                        extraData.$pair.append(responseToolbarTpl({
                            interactionSerial : interaction.getSerial(),
                            choiceSerial : 'n/a',
                            choiceIdentifier : 'n/a'
                        }));

                        $miniToolbar = extraData.$pair.children('.mini-tlb');
                    }
                    $miniToolbar.show();
                    $miniToolbar.data('pairIdentifier', pairIdentifier);

                    var $correct = $miniToolbar.find('[data-role=correct]').data('pairIdentifier', pairIdentifier);
                    if(pairs[pairIdentifier] && pairs[pairIdentifier].correct){
                        $correct.prop('checked', true);
                    }else{
                        $correct.prop('checked', false);
                    }

                    var $score = $miniToolbar.find('[data-role=score]').data('pairIdentifier', pairIdentifier);
                    if(pairs[pairIdentifier] && pairs[pairIdentifier].score){
                        $score.val(pairs[pairIdentifier].score);
                    }else{
                        //@todo set _defaultMappingValue as placeholder text:
                        $score.val(_defaultMappingValue);
                    }
                }

            }else{

                //reset and hide toolbar
                $miniToolbar = extraData.$pair.children('.mini-tlb');
                $miniToolbar.hide();

                //before resetting the correct and score inputs, remove the current map entry from the response:
                response.removeMapEntry($miniToolbar.data('pairIdentifier'));

                $miniToolbar.removeData('pairIdentifier');
                $miniToolbar.find('[data-role=correct]').prop('checked', false).removeData('pairIdentifier');
                $miniToolbar.find('[data-role=score]').val(_defaultMappingValue).removeData('pairIdentifier');

                //finally update the correct response
                _saveCorrect();
            }
        });

        _widget.$container.find('.result-area')
            .on('change', '[data-role=correct]', _saveCorrect)
            .on('keyup', '[data-role=score]', function(){
            var $score = $(this);
            response.setMapEntry($score.data('pairIdentifier'), $score.val());
        });
    };

    return AssociateInteractionStateCorrect;
});
