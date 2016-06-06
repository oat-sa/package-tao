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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */
define([
    'lodash',
    'jquery',
    'ui/selecter',
    'tpl!taoQtiItem/qtiCreator/tpl/modalFeedback/rule',
    'tpl!taoQtiItem/qtiCreator/tpl/modalFeedback/panel',
    'taoQtiItem/qtiCreator/widgets/helpers/modalFeedbackConditions'
], function(_, $, selecter, ruleTpl, panelTpl, modalFeedbackConditions){
    'use strict';

    var _renderFeedbackRule = function(feedbackRule){

        var feedbackElseSerial,
            feedbackElse = feedbackRule.feedbackElse,
            addElse = !feedbackElse;

        if(feedbackElse){
            feedbackElseSerial = feedbackElse.serial;
        }
        var availableConditions = modalFeedbackConditions.get(feedbackRule.comparedOutcome);
        var rule =  ruleTpl({
            availableConditions : availableConditions,
            serial : feedbackRule.serial,
            condition : feedbackRule.condition,
            comparedValue : feedbackRule.comparedValue,
            feedbackThen : feedbackRule.feedbackThen.serial,
            feedbackElse : feedbackElseSerial,
            addElse : addElse,
            hideScore : (feedbackRule.condition === 'correct' || feedbackRule.condition === 'incorrect' || feedbackRule.condition === 'choices')//@todo remove this, put in init()
        });

        var $rule = $(rule);

        selecter($rule);

        //init rule editing
        var condition = _.find(availableConditions, {name : feedbackRule.condition});
        condition.init(feedbackRule, $rule.find('select.feedbackRule-condition'));

        return $rule;
    };

    var _initFeedbackEventListener = function($feedbacksPanel, response){

        var $feedbacks = $feedbacksPanel.find('.feedbackRules');
        $feedbacksPanel.on('click', '.feedbackRule-add', function(e){
            e.preventDefault();

            var feedbackRule = response.createFeedbackRule(),
                $lastRule = $feedbacks.children('.feedbackRule-container:last');

            if($lastRule.length){
                $lastRule.after(_renderFeedbackRule(feedbackRule));
            }else{
                $feedbacks.html(_renderFeedbackRule(feedbackRule));
            }
        }).on('click', '.feedbackRule-add-else', function(e){

            e.preventDefault();

            var $fbContainer = $(this).parents('.feedbackRule-container'),
                fbSerial = $fbContainer.data('serial'),
                fbRule = response.getFeedbackRule(fbSerial),
                fbModal = response.createFeedbackElse(fbRule);

            $fbContainer.replaceWith(_renderFeedbackRule(fbRule));

        }).on('click', '.feedbackRule-button-delete', function(){
            
            var $deleteButton = $(this),
                $fbContainer = $deleteButton.parents('.feedbackRule-container'),
                fbSerial = $fbContainer.data('serial'),
                fbRule = response.getFeedbackRule(fbSerial);

            switch($deleteButton.data('role')){
                case 'rule':
                    response.deleteFeedbackRule(fbRule);
                    $fbContainer.remove();
                    break;
                case 'else':
                    response.deleteFeedbackElse(fbRule);
                    $fbContainer.replaceWith(_renderFeedbackRule(fbRule));//replace all to display the add "else" button
                    break;
            }
        }).on('change', '.feedbackRule-condition', function(){

            var $select = $(this),
                condition = $select.val(),
                availableConditions = modalFeedbackConditions.get(response),
                fbRule = response.getFeedbackRule($(this).parents('.feedbackRule-container').data('serial')),
                newCondition = _.find(availableConditions, {name : condition}),
                oldCondition = _.find(availableConditions, {name : fbRule.condition});

            //exec unset old condition callback
            if(oldCondition && _.isFunction(oldCondition.onUnset)){
                oldCondition.onUnset(fbRule, $select);
            }

            //exec set new condition callback
            if(newCondition && _.isFunction(newCondition.onSet)){
                newCondition.onSet(fbRule, $select);
            }

            //init the new condition editing
            newCondition.init(fbRule, $select);

        }).on('click', '[data-feedback]', function(){

            var $btn = $(this),
                fbRule = response.getFeedbackRule($btn.parents('.feedbackRule-container').data('serial')),
                modalFeedback,
                modalFeedbackWidget;

            switch($btn.data('feedback')){
                case 'then':
                    modalFeedback = fbRule.feedbackThen;
                    break;
                case 'else':
                    modalFeedback = fbRule.feedbackElse;
                    break;
            }

            if(modalFeedback){
                //show modal feedback editor:
                modalFeedbackWidget = _getModalFeedbackWidget(modalFeedback);
                modalFeedbackWidget.changeState('active');
            }

        });

    };

    var _widgets = {};

    var _getModalFeedbackWidget = function(modalFeedback){

        var $feedbacksContainer = $('#modalFeedbacks');
        if(!_widgets[modalFeedback.serial]){
            $feedbacksContainer.append(modalFeedback.render());
            modalFeedback.postRender();
            _widgets[modalFeedback.serial] = modalFeedback.data('widget');
        }

        return _widgets[modalFeedback.serial];
    };

    return {
        initFeedbacksPanel : function($feedbacksPanel, response){

            $feedbacksPanel.html(panelTpl());

            var $feedbackRules = $feedbacksPanel.find('.feedbackRules'),
                feedbackRules = response.getFeedbackRules();

            if(feedbackRules && _.size(feedbackRules)){
                $feedbackRules.empty();
                _.each(feedbackRules, function(feedbackRule){
                    $feedbackRules.append(_renderFeedbackRule(feedbackRule));
                });
            }

            _initFeedbackEventListener($feedbacksPanel, response);
        },
        renderFeedbackRule : _renderFeedbackRule
    };
});
