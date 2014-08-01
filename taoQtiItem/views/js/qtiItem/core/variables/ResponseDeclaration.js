define(['taoQtiItem/qtiItem/core/variables/VariableDeclaration', 'lodash'], function(VariableDeclaration, _){

    var ResponseDeclaration = VariableDeclaration.extend({
        qtiClass : 'responseDeclaration',
        init : function(serial, attributes){

            this._super(serial, attributes);

            //MATCH_CORRECT, MAP_RESPONSE, MAP_RESPONSE_POINT
            this.template = '';//previously called 'howMatch'

            //when template equals ont of the "map" one (MAP_RESPONSE, MAP_RESPONSE_POINT)
            this.mappingAttributes = {};
            this.mapEntries = {};

            //correct response [0..*]
            this.correctResponse = null;

            //tao internal usage:
            this.feedbackRules = {};
        },
        getFeedbackRules : function(){
            return [];
        },
        getComposingElements : function(){
            var elts = this._super();
            elts = _.extend(elts, this.getFeedbackRules());
            return elts;
        },
        toArray : function(){
            var arr = this._super();
            arr.howMatch = this.template;
            arr.correctResponses = this.correctResponse;
            arr.mapping = this.mapEntries;
            arr.mappingAttributes = this.mappingAttributes;
            return arr;
        }
    });

    return ResponseDeclaration;
});


