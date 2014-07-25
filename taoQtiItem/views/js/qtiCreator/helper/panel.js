define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCreator/editor/editor'
], function($, _, Element, editor){

    var _getItemContainer = function(){
        return $('#item-editor-panel');
    };

    var showPanel = function($panel, $fold){

        $panel.show();
        editor.openSections($panel.children('section'));

        if($fold && $fold.length){
            editor.closeSections($fold.children('section'));
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
            math : 'Math'
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
            $appearanceToggler = $('#appearance-trigger');

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
            }else{
                $appearanceToggler.removeClass('active');
                $formStylePanel.hide();
                showPanel($formItemPanel);
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
                editor.enableSubGroup('inline-interactions');
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
            editor.enableSubGroup('inline-interactions');
        }else{
            editor.disableSubGroup('inline-interactions');
        }
    };

    return {
        initFormVisibilityListener : initFormVisibilityListener,
        showPanel : showPanel,
        toggleInlineInteractionGroup : toggleInlineInteractionGroup
    };

});