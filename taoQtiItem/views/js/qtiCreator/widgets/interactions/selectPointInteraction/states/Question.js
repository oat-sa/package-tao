/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'util/image',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/imageSelector',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/selectPoint'
], function($, _, imageUtil, stateFactory, Question, imageSelector, formElement, interactionFormElement, formTpl){

    
    /**
     * The question state for the selectPoint interaction
     * @extends taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question
     * @exports taoQtiItem/qtiCreator/widgets/interactions/selectPointInteraction/states/Question
     */
    var SelectPointInteractionStateQuestion = stateFactory.extend(Question);

    /**
     * Initialize the form linked to the interaction
     */
    SelectPointInteractionStateQuestion.prototype.initForm = function(){

        var widget = this.widget;
        var options = widget.options;
        var interaction = widget.element;
        var $form = widget.$form;

        $form.html(formTpl({
            baseUrl         : options.baseUrl,
            maxChoices      : parseInt(interaction.attr('maxChoices')),
            minChoices      : parseInt(interaction.attr('minChoices')),
            choicesCount    : _.size(interaction.getChoices()),
            data            : interaction.object.attr('data'),
            width           : interaction.object.attr('width'),
            height          : interaction.object.attr('height'),
            type            : interaction.object.attr('type')
        }));

        imageSelector($form, options); 

        formElement.initWidget($form);
        
        //init data change callbacks
        var callbacks = formElement.getMinMaxAttributeCallbacks(this.widget.$form, 'minChoices', 'maxChoices');
        callbacks.data = function(inteaction, value){
            interaction.object.attr('data', value);
            widget.rebuild({
                ready:function(widget){
                    widget.changeState('question');
                }
            });
        };
        callbacks.width = function(inteaction, value){
            interaction.object.attr('width', value);
        };
        callbacks.height = function(inteaction, value){
            interaction.object.attr('height', value);
        };
        callbacks.type = function(inteaction, value){
            if(!value || value === ''){
                interaction.object.removeAttr('type');
            } else {
                interaction.object.attr('type', value);
            }
        };
        formElement.setChangeCallbacks($form, interaction, callbacks, { validateOnInit : false });
        
        interactionFormElement.syncMaxChoices(widget);
    };

    return SelectPointInteractionStateQuestion;
});
