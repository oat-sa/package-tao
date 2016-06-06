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
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/associate',
    'taoQtiItem/qtiCommonRenderer/helpers/sizeAdapter'
], function(stateFactory, Question, formElement, formTpl, sizeAdapter){

    'use strict';

    var AssociateInteractionStateQuestion = stateFactory.extend(Question);

    AssociateInteractionStateQuestion.prototype.initForm = function(){

       var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;

        $form.html(formTpl({
            shuffle : !!interaction.attr('shuffle'),
            minAssociations : parseInt(interaction.attr('minAssociations')),
            maxAssociations : parseInt(interaction.attr('maxAssociations'))
        }));

        formElement.initWidget($form);
        
        //init data change callbacks
        var callbacks = formElement.getMinMaxAttributeCallbacks(this.widget.$form, 'minAssociations', 'maxAssociations');
        callbacks.shuffle = formElement.getAttributeChangeCallback();
        formElement.setChangeCallbacks($form, interaction, callbacks);
        
        //adapt size
        sizeAdapter.adaptSize(_widget);
        _widget.on('choiceCreated', function(){
            sizeAdapter.adaptSize(_widget);
        });
    };
    
    return AssociateInteractionStateQuestion;
});
