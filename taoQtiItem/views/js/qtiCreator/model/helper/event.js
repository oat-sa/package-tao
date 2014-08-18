define(['jquery', 'taoQtiItem/qtiItem/core/Element'], function($, Element){

    return {
        choiceCreated : function(choice, interaction){
            $(document).trigger('choiceCreated.qti-widget', {choice : choice, interaction : interaction});
        },
        deleted : function(element, parent){
            Element.unsetElement(element.getSerial());
            $(document).off('.' + element.getSerial());
            $(document).trigger('deleted.qti-widget', {element : element, parent : parent});
        },
        getList : function(){
        
            return [
                'containerBodyChange',
                'containerElementAdded',
                'elementCreated.qti-widget',
                'attributeChange.qti-widget',
                'choiceCreated.qti-widget',
                'correctResponseChange.qti-widget',
                'mapEntryChange.qti-widget',
                'mapEntryRemove.qti-widget',
                'deleted.qti-widget',
                'choiceTextChange.qti-widget',
                'responseTemplateChange.qti-widget',
                'mappingAttributeChange.qti-widget',
                'feedbackRuleConditionChange.qti-widget',
                'feedbackRuleCreated.qti-widget',
                'feedbackRuleRemoved.qti-widget',
                'feedbackRuleElseCreated.qti-widget',
                'feedbackRuleElseRemoved.qti-widget'
            ];
        }
    }
});