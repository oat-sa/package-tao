define([
    'lodash',
    'taoQtiItem/qtiCreator/widgets/interactions/inlineInteraction/Widget',
    'taoQtiItem/qtiCreator/widgets/interactions/endAttemptInteraction/states/states',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/endAttemptInteraction'
], function(_, InteractionWidget, states, interactionTpl){
    'use strict';
    var EndAttemptInteractionWidget = InteractionWidget.clone();
    
    /**
     * Init the creator
     */
    EndAttemptInteractionWidget.initCreator = function(){

        this.registerStates(states);

        InteractionWidget.initCreator.call(this);

        //remove toolbar title, because it is too large
        this.$container.find('.tlb-title').remove();
    };
    
    /**
     * Render the interaction widget from template
     * 
     * @returns {Object}
     */
    EndAttemptInteractionWidget.renderInteraction = function(){

        var interaction = this.element,
            tplData = {
            tag : interaction.qtiClass,
            serial : interaction.serial,
            attributes : interaction.attributes
        };

        return interactionTpl(tplData);
    };
    
    /**
     * Prepare and define the end attempt interaction widget container
     */
    EndAttemptInteractionWidget.buildContainer = function(){
        var previous, next;
        //add a space to be able to place the cursor before and after it.
        if(this.$original.length){
            previous = this.$original[0].previousSibling;
            next = this.$original[0].nextSibling;

            if(!previous || (previous.nodeType === 3 && previous.nodeValue === '') || previous.nodeType !== 3){
                this.$original.before('&nbsp;');
            }
            if(!next || (next.nodeType === 3 && next.nodeValue === '') || next.nodeType !== 3){
                this.$original.after('&nbsp;');
            }
        }

        //set the itemContainer where the actual widget should be append and be positioned absolutely
        this.$itemContainer = this.$original.parents('.item-editor-item');

        //prepare html: interaction & choices:
        this.$itemContainer.append(this.renderInteraction());
        
        this.$container = this.$itemContainer.find('.widget-endAttemptInteraction[data-serial=' + this.element.getSerial() + ']');
    };

    return EndAttemptInteractionWidget;
});
