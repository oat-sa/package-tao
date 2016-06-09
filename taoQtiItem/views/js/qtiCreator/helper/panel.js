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
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element'
], function($, _, Element){
    "use strict";
    var _getItemContainer = function(){
        return $('#item-editor-panel');
    };

    var showPanel = function($panel, $fold){

        $panel.show();
        openSections($panel.children('section'));

        if($fold && $fold.length){
            closeSections($fold.children('section'));
        }
    };

    var initFormVisibilityListener = function(){

        //first of all, clear all listener
        $(document).off('.panel');

        var $itemContainer = _getItemContainer();

        var _staticElements = {
            img : 'Image',
            object : 'Media',
            rubricBlock : 'Rubric Block',
            math : 'Math',
            include : 'Shared Stimulus'
        };

        // all sections on the right sidebar are invisible by default
        var $formInteractionPanel = $('#item-editor-interaction-property-bar'),
            $formChoicePanel = $('#item-editor-choice-property-bar'),
            $formResponsePanel = $('#item-editor-response-property-bar'),
            $formItemPanel = $('#item-editor-item-property-bar'),
            $formBodyElementPanel = $('#item-editor-body-element-property-bar'),
            $formTextBlockPanel = $('#item-editor-text-property-bar'),
            $formModalFeedbackPanel = $('#item-editor-modal-feedback-property-bar'),
            $formStylePanel = $('#item-style-editor-bar'),
            $appearanceToggler = $('#appearance-trigger'),
            $menuLabel = $appearanceToggler.find('.menu-label'),
            $itemIcon = $appearanceToggler.find('.icon-item'),
            $styleIcon = $appearanceToggler.find('.icon-style');

        var _toggleAppearanceEditor = function(active){

            if(active){

                $appearanceToggler.addClass('active');
                $formStylePanel.show();
                $formItemPanel.hide();

                //current widget sleep:
                $itemContainer.trigger('styleedit');

                /* At the time of writing this the following sections are available:
                 *
                 * #sidebar-left-section-text
                 * #sidebar-left-section-block-interactions
                 * #sidebar-left-section-inline-interactions
                 * #sidebar-left-section-graphic-interactions
                 * #sidebar-left-section-media
                 * #sidebar-right-css-manager
                 * #sidebar-right-style-editor
                 * #sidebar-right-item-properties
                 * #sidebar-right-body-element-properties
                 * #sidebar-right-text-block-properties
                 * #sidebar-right-interaction-properties
                 * #sidebar-right-choice-properties
                 * #sidebar-right-response-properties
                 */
                showPanel($formStylePanel);
                $menuLabel.text($menuLabel.data('item'));
                $itemIcon.show();
                $styleIcon.hide();
            }else{
                $appearanceToggler.removeClass('active');
                $formStylePanel.hide();
                showPanel($formItemPanel);
                $menuLabel.text($menuLabel.data('style'));
                $itemIcon.hide();
                $styleIcon.show();
            }
        };

        $appearanceToggler.on('click', function(){

            if($appearanceToggler.hasClass('active')){
                _toggleAppearanceEditor(false);
            }else{
                _toggleAppearanceEditor(true);
            }
        });

        //@todo : fix this timeout event
        _.delay(function(){
            showPanel($formItemPanel);
        }, 200);

        $(document).on('afterStateInit.qti-widget.panel', function(e, element, state){

            switch(state.name){
                case 'active':

                    _toggleAppearanceEditor(false);
                    if(!Element.isA(element, 'assessmentItem')){
                        $formItemPanel.hide();
                    }

                    var label = _staticElements[element.qtiClass];
                    if(label){
                        $formBodyElementPanel.find('h2').html(label + ' Properties');
                        showPanel($formBodyElementPanel);
                    }else if(element.qtiClass === '_container'){
                        showPanel($formTextBlockPanel);
                    }

                    if(element.qtiClass === 'modalFeedback'){
                        showPanel($formModalFeedbackPanel);
                        $formResponsePanel.hide();
                    }
                    break;

                case 'question':

                    showPanel($formInteractionPanel);
                    break;

                case 'answer':

                    showPanel($formResponsePanel);
                    break;

                case 'choice':
                    showPanel($formChoicePanel, $formInteractionPanel);
                    break;

                case 'sleep':

                    if(_staticElements[element.qtiClass]){
                        $formBodyElementPanel.hide();
                    }else if(element.qtiClass === '_container'){
                        $formTextBlockPanel.hide();
                    }

                    if(!Element.isA(element, 'choice')){
                        if(!$itemContainer.find('.widget-box.edit-active').length){
                            showPanel($formItemPanel);
                        }
                    }
                    break;
            }

        }).on('afterStateExit.qti-widget.panel', function(e, element, state){

            switch(state.name){
                case 'active':
                    if(element.qtiClass === 'modalFeedback'){
                        showPanel($formResponsePanel);
                        $formModalFeedbackPanel.hide();
                    }
                    break;
                case 'question':
                    if(element.is('interaction')){
                        $formChoicePanel.hide();
                        $formInteractionPanel.hide();
                    }
                    break;
                case 'choice':
                    $formChoicePanel.hide();
                    showPanel($formInteractionPanel);
                    break;
                case 'answer':
                    $formResponsePanel.hide();
                    break;
            }

        }).on('elementCreated.qti-widget.panel', function(e, data){

            if(data.element.qtiClass === '_container'){
                enableSubGroup('inline-interactions');
            }

        }).on('deleted.qti-widget.panel', function(e, data){

            if(data.element.qtiClass === '_container'){
                toggleInlineInteractionGroup();
            }

        });
    };

    var toggleInlineInteractionGroup = function(){

        var $itemContainer = _getItemContainer();
        if($itemContainer.find('.widget-textBlock').length){
            enableSubGroup('inline-interactions');
        }else{
            disableSubGroup('inline-interactions');
        }
    };

    // selectors and classes
    var heading = 'h2',
        section = 'section',
        panel = 'hr, .panel',
        closed = 'closed',
        ns = 'accordion';

    var initSidebarAccordion = function($sidebar){

        var $sections = $sidebar.find(section),
            $allPanels = $sidebar.children(panel).hide(),
            $allTriggers = $sidebar.find(heading);

        if($allTriggers.length === 0){
            return true;
        }

        // setup events
        $allTriggers.each(function(){
            var $heading = $(this),
                $section = $heading.parents(section),
                $panel = $section.children(panel),
                $closer = $('<span>', {'class' : 'icon-up'}),
                $opener = $('<span>', {'class' : 'icon-down'}),
                action = $panel.is(':visible') ? 'open' : 'close';

            $heading.append($closer).append($opener).addClass(closed);

            // this allows multiple calls, required when blocks are added dynamically
            if($heading.hasClass('_accordion')) {
                return;
            }
            else {
                $heading.addClass('_accordion');
            }

            // toggle heading class arrow (actually switch arrow)
            $panel.on('panelclose.' + ns + ' panelopen.' + ns, function(e, args){
                var fn = e.type === 'panelclose' ? 'add' : 'remove';
                args.heading[fn + 'Class'](closed);
            });

            $panel.trigger('panel' + action + '.' + ns, {heading : $heading});

        });

        $sections.each(function(){

            // assign click action to headings
            $(this).find(heading).on('click', function(e, args){

                var $heading = $(this),
                    $panel = $heading.parents(section).children(panel),
                    preserveOthers = !!(args && args.preserveOthers),
                    actions = {
                        close : 'hide',
                        open : 'fadeIn'
                    },
                action,
                    forceState = (args && args.forceState ? args.forceState : false),
                    classFn;

                if(forceState){
                    classFn = forceState === 'open' ? 'addClass' : 'removeClass';
                    $heading[classFn](closed);
                }

                action = $heading.hasClass(closed) ? 'open' : 'close';

                // whether or not to close other sections in the same sidebar
                // @todo (optional): remove 'false' in the condition below
                // to change the style to accordion, i.e. to allow for only one open section
                if(false && !preserveOthers){
                    $allPanels.not($panel).each(function(){
                        var $panel = $(this),
                            $heading = $panel.parent().find(heading),
                            _action = 'close';

                        $panel.trigger('panel' + _action + '.' + ns, {heading : $heading})[actions[_action]]();
                    });
                }

                $panel.trigger('panel' + action + '.' + ns, {heading : $heading})[actions[action]]();
            });

        });
    };

    /**
     * Toggle section display
     *
     * @param sections
     */
    var _toggleSections = function(sections, preserveOthers, state){
        sections.each(function(){
            $(this).find(heading).trigger('click', {preserveOthers : preserveOthers, forceState : state});
        });
    }

    /**
     * Close specific sections
     *
     * @param sections
     */
    var closeSections = function(sections, preserveOthers){
        _toggleSections(sections, !!preserveOthers, 'close');
    };

    /**
     * Open specific sections
     *
     * @param sections
     */
    var openSections = function(sections, preserveOthers){
        _toggleSections(sections, !!preserveOthers, 'open');
    };

    /**
     * toggle availability of sub group
     * @param subGroup
     */
    var _toggleSubGroup = function(subGroup, state){
        subGroup = $('.' + subGroup);
        if(subGroup.length){
            var fn = state === 'disable' ? 'addClass' : 'removeClass';
            subGroup.data('cover')[fn]('blocking');
        }
    };

    /**
     * enable sub group
     * @param subGroup
     */
    var enableSubGroup = function(subGroup){
        _toggleSubGroup(subGroup, 'enable');
    };

    /**
     * disable sub group
     * @param subGroup
     */
    var disableSubGroup = function(subGroup){
        _toggleSubGroup(subGroup, 'disable');
    };

    return {
        initFormVisibilityListener : initFormVisibilityListener,
        showPanel : showPanel,
        toggleInlineInteractionGroup : toggleInlineInteractionGroup,
        initSidebarAccordion : initSidebarAccordion,
        openSections : openSections,
        closeSections : closeSections,
        enableSubGroup : enableSubGroup,
        disableSubGroup : disableSubGroup
    };

});
