define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiItem/core/variables/ResponseDeclaration',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/response/SimpleFeedbackRule',
    'taoQtiItem/qtiItem/helper/response'
], function($, _, Element, ResponseDeclaration, editable, SimpleFeedbackRule, responseHelper){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        setTemplate : function(template){
            var templateUri = responseHelper.getTemplateUriFromName(template);
            if(templateUri && this.template !== templateUri){
                this.template = templateUri;
                $(document).trigger('responseTemplateChange.qti-widget', {'element' : this, 'value' : template});
            }
            return this;
        },
        getTemplate : function(){
            return this.template;
        },
        resetCorrect : function(){
            this.correctResponse = null;
            $(document).trigger('correctResponseChange.qti-widget', {'element' : this, 'value' : null});
            return this;
        },
        setCorrect : function(value){
            if(_.isString(value)){
                value = [value];
            }
            this.correctResponse = value;
            $(document).trigger('correctResponseChange.qti-widget', {'element' : this, 'value' : value});
            return this;
        },
        getCorrect : function(){
            return _.clone(this.correctResponse);
        },
        setMappingAttribute : function(name, value){
            this.mappingAttributes[name] = value;
            $(document).trigger('mappingAttributeChange.qti-widget', {'element' : this, 'key' : name, 'value' : value});
        },
        removeMappingAttribute : function(name){
            delete this.mappingAttributes[name];
            $(document).trigger('mappingAttributeChange.qti-widget', {'element' : this, 'key' : name, 'value' : null});
        },
        getMappingAttribute : function(name){
            return this.mappingAttributes[name];
        },
        setMapEntry : function(mapKey, mappedValue, caseSensitive){

            mappedValue = parseFloat(mappedValue);
            caseSensitive = caseSensitive ? true : false;

            if(!isNaN(mappedValue)){
                if(this.attr('cardinality') === 'multiple' && this.attr('baseType') === 'pair'){
                    //in this case, A-B is equivalent to B-A so need to check if any of those conbination already exists:

                    var mapKeys = mapKey.split(' '),
                        mapKeysReverse = mapKeys[1] + ' ' + mapKeys[0];

                    if(this.mapEntries[mapKeysReverse]){
                        this.mapEntries[mapKeysReverse] = mappedValue;
                    }else{
                        this.mapEntries[mapKey] = mappedValue;
                    }
                }else{
                    this.mapEntries[mapKey] = mappedValue;
                }

                /**
                 * @todo caseSensitive is always set to "false" currently, need to add an option for this
                 * this.mapEntries[mapKey] = {
                 'mappedValue' : mappedValue,
                 'caseSensitive' : caseSensitive
                 };
                 */

                $(document).trigger('mapEntryChange.qti-widget', {
                    element : this,
                    mapKey : mapKey,
                    mappedValue : mappedValue,
                    caseSensitive : caseSensitive
                });
            }else{
                throw 'the mapped value is not a number';
            }

            return this;
        },
        removeMapEntry : function(mapKey, canBeEmpty){
            //is there a opportunity mapKey to be empty string
            if(typeof canBeEmpty == 'undefined'){
                canBeEmpty = false;
            }

            if(mapKey || canBeEmpty){
                if(this.attr('cardinality') === 'multiple' && this.attr('baseType') === 'pair'){
                    //in this case, A-B is equivalent to B-A so need to check if any of those conbination already exists:
                    var mapKeys = mapKey.split(' '),
                        mapKeysReverse = mapKeys[1] + ' ' + mapKeys[0];

                    delete this.mapEntries[mapKeysReverse];
                }
                delete this.mapEntries[mapKey];

                $(document).trigger('mapEntryRemove.qti-widget', {element : this, mapKey : mapKey});
            }

            return this;
        },
        getMapEntries : function(){
            return _.clone(this.mapEntries);
        },
        removeMapEntries : function(){
            _(this.mapEntries).keys().forEach(this.removeMapEntry, this);
        },
        createFeedbackRule : function(){

            var item = this.getRelatedItem();

            var outcome = item.createOutcomeDeclaration({
                identifier : 'FEEDBACK',
                cardinality : 'single',
                baseType : 'identifier'
            });
            
            var modalFeedback = item.createModalFeedback({
                identifier : 'feedbackModal',
                outcomeIdentifier : outcome.id()
            }, this);
            
            var rule = new SimpleFeedbackRule('', outcome, modalFeedback);

            rule.setCondition(this, 'correct');
            this.feedbackRules[rule.getSerial()] = rule;

            //set renderer
            var renderer = this.getRenderer();
            if(renderer){
                rule.setRenderer(renderer);
                modalFeedback.setRenderer(renderer);
            }

            //trigger creation event
            $(document).trigger('feedbackRuleCreated.qti-widget', {element : this, rule : rule});

            return rule;
        },
        getFeedbackRule : function(serial){
            return this.feedbackRules[serial];
        },
        getFeedbackRules : function(){
            return _.clone(this.feedbackRules);
        },
        setCondition : function(rule, condition, value){
            rule.setCondition(this, condition, value);
            $(document).trigger('feedbackRuleConditionChange.qti-widget', {element : this, rule : rule, condition : condition, value : value});
        },
        deleteFeedbackRule : function(rule){

            var item = this.getRelatedItem(), ret;

            item.remove('outcomes', rule.feedbackOutcome);
            item.remove('modalFeedbacks', rule.feedbackThen);
            item.remove('modalFeedbacks', rule.feedbackElse || '');//feedback "else" is optional

            ret = this.remove('feedbackRules', rule);

            $(document).trigger('feedbackRuleRemoved.qti-widget', {element : this, rule : rule});

            return ret;
        },
        createFeedbackElse : function(rule){

            var modalFeedback;

            if(Element.isA(rule, '_simpleFeedbackRule')){

                modalFeedback = this.getRelatedItem().createModalFeedback({
                    identifier : 'feedbackModal',
                    outcomeIdentifier : rule.feedbackOutcome.id()
                }, this);

                rule.setFeedbackElse(modalFeedback);

                //set renderer
                var renderer = this.getRenderer();
                if(renderer){
                    modalFeedback.setRenderer(renderer);
                }

                $(document).trigger('feedbackRuleElseCreated.qti-widget', {element : this, rule : rule, modalFeedback : modalFeedback});
            }

            return modalFeedback;
        },
        deleteFeedbackElse : function(rule){

            this.getRelatedItem().remove('modalFeedbacks', rule.feedbackElse);
            rule.feedbackElse = null;

            $(document).trigger('feedbackRuleElseRemoved.qti-widget', {element : this, rule : rule});
        },
        getDefaultAttributes : function(){
            return {};
        }
    });

    return ResponseDeclaration.extend(methods);
});
