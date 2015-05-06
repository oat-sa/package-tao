define([
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/AssociateInteraction',
    'taoQtiItem/qtiCreator/widgets/interactions/associateInteraction/helper',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'lodash',
    'i18n'
], function(commonRenderer, creatorHelper, commonHelper, _, __){

    var ResponseWidget = {
        create : function(widget, responseMappingMode){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            if(responseMappingMode){
                commonHelper.appendInstruction(widget.element, __('Please define association pairs and their scores below.'));
                interaction.responseMappingMode = true;
            }else{
                commonHelper.appendInstruction(widget.element, __('Please define the correct association pairs below.'));
            }

            commonRenderer.render(interaction);
            
            creatorHelper.adaptSize(widget);
        },
        setResponse : function(interaction, response){
            var responseDeclaration = interaction.getResponseDeclaration();
            commonRenderer.setResponse(interaction, ResponseWidget.formatResponse(response, responseDeclaration.attr('cardinality')));
            
            creatorHelper.adaptSize(interaction.data('widget'));
            
        },
        destroy : function(widget){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            delete interaction.responseMappingMode;

            commonRenderer.renderEmptyPairs(interaction);
            
            creatorHelper.adaptSize(widget);
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
