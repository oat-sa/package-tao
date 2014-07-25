define([
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/AssociateInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'lodash',
    'i18n'
], function(commonRenderer, helper, _, __){

    var ResponseWidget = {
        create : function(widget, responseMappingMode){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            if(responseMappingMode){
                helper.appendInstruction(widget.element, __('Please define association pairs and their scores below.'));
                interaction.responseMappingMode = true;
            }else{
                helper.appendInstruction(widget.element, __('Please define the correct association pairs below.'));
            }

            commonRenderer.render(interaction);
        },
        setResponse : function(interaction, response){
            var responseDeclaration = interaction.getResponseDeclaration();
            commonRenderer.setResponse(interaction, this.formatResponse(response, responseDeclaration.attr('cardinality')));
        },
        destroy : function(widget){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            delete interaction.responseMappingMode;

            commonRenderer.renderEmptyPairs(interaction);
        },
        getResponseSummary : function(responseDeclaration){
            
            var pairs = [],
                correctResponse = _.values(responseDeclaration.getCorrect()),
                mapEntries = responseDeclaration.getMapEntries();
            
            _.each(correctResponse, function(pair) {

                var sortedIdPair = pair.split(' ').sort(),
                    sortedIdPairKey = sortedIdPair.join(' ');

                pairs[sortedIdPairKey] = {
                    pair: sortedIdPair,
                    correct: true,
                    score: undefined
                };
            });

            _.forIn(mapEntries, function(score, pair) {

                var sortedIdPair = pair.split(' ').sort(),
                    sortedIdPairKey = sortedIdPair.join(' ');

                if (!pairs[sortedIdPairKey]) {
                    pairs[sortedIdPairKey] = {
                        pair: sortedIdPair,
                        correct: false,
                        score: score
                    };
                } else {
                    pairs[sortedIdPairKey].score = score;
                }
            });
            
            return pairs;
        },
        formatResponse : function(response, cardinality){

            var formatedRes;
            if(cardinality === 'single'){
                formatedRes = {base : { pair : [] }};
            } else {
                formatedRes = {list : { pair : [] }};
            }
            
            _.each(response, function(pairString){
                var pair = pairString.split(' ');
                if(cardinality === 'single'){
                    formatedRes.base.pair = pair;
                } else {
                    formatedRes.list.pair.push(pair);
                }
            });

            return formatedRes;
        },
        unformatResponse : function(formatedResponse){

            var res = [];

            if(formatedResponse.list && formatedResponse.list.pair){
                _.each(formatedResponse.list.pair, function(pair){
                    res.push(pair.join(' '));
                });
            }else if(formatedResponse.base && formatedResponse.base.pair){
                res.push(formatedResponse.base.pair.join(' '));
            }
            return res;
        }
    };

    return ResponseWidget;
});
