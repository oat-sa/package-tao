define([
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/helper/uploadMime',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/upload'
], function(_, __, stateFactory, Question, formElement, uploadHelper, formTpl){

    var UploadInteractionStateQuestion = stateFactory.extend(Question);

    UploadInteractionStateQuestion.prototype.initForm = function() {
        var _widget = this.widget,
            $form = _widget.$form,
            interaction = _widget.element;
        
        var types = uploadHelper.getMimeTypes();
        types.unshift({ "mime" : "any/kind", "label" : __("-- Any kind of file --") });
        
        // Prepare the work...
        if (typeof interaction.attr('type') !== 'undefined') {
            
            if (interaction.attr('type') === '') {
                // Kill the attribute if it is empty.
                delete interaction.attributes['type'];
            }
            else {
                // Pre-select a value in the types combo box if needed.
                for (var i in types) {
                    if (interaction.attr('type') == types[i].mime) {
                        types[i].selected = true;
                    }
                }
            }
        }
        
        $form.html(formTpl({
            "types" : types
        }));

        formElement.initWidget($form);
        
        callbacks = {};
        
        // -- type callback.
        callbacks['type'] = function(interaction, attrValue){
            
            if (attrValue === 'any/kind') {
                interaction.removeAttr('type');
            }
            else {
                interaction.attr('type', attrValue);
            }
        };

        //init data change callbacks
        formElement.setChangeCallbacks($form, interaction, callbacks);
    };

    return UploadInteractionStateQuestion;
});