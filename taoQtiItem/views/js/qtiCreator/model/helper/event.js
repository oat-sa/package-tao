define(['jquery', 'lodash'], function($, _){
    "use strict";
    var _ns = '.qti-creator';
    var _ns_model = '.qti-creator';
    var eventList = [
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

    var event = {
        choiceCreated : function(choice, interaction){
            $(document).trigger('choiceCreated.qti-widget', {choice : choice, interaction : interaction});
        },
        deleted : function(element, parent){

            if(element.isset()){
                element.unset();
            }

            $(document).off('.' + element.getSerial());
            $(document).trigger('deleted.qti-widget', {element : element, parent : parent});
        },
        getList : function(addedNamespace){
            var events = _.clone(eventList);
            if(addedNamespace){
                return _.map(events, function(e){
                    return e + '.' + addedNamespace;
                });
            }else{
                return events;
            }
        },
        initElementToWidgetListeners : function(){

            var ns = '.widget-container';

            //forward all event to the widget $container
            $(document).off(ns).on(event.getList(ns).join(' '), function(e, data){
                var element = data.element || data.container || null;
                if(data && element && element.data('widget')){
                    element.data('widget').$container.trigger(e.type + _ns + _ns_model, data);
                }
            });

        },
        getNs : function(){
            return _ns;
        },
        getNsModel : function(){
            return _ns_model;
        }
    };

    return event;
});