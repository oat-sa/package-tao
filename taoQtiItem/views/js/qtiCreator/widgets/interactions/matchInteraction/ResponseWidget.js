define([
    'jquery',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MatchInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/instructions/instructionManager',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/matchInteraction.score',
    'lodash',
    'i18n',
    'polyfill/placeholders'
], function($, commonRenderer, instructionMgr, formElement, scoreTpl, _, __){

    var ResponseWidget = {
        create : function(widget, responseMappingMode){

            var interaction = widget.element;

            commonRenderer.resetResponse(interaction);
            commonRenderer.destroy(interaction);

            if(responseMappingMode){
                instructionMgr.appendInstruction(widget.element, __('Please define the correct response and the score below.'));
                interaction.data('responseMappingMode', true);
                ResponseWidget.createScoreWidgets(widget);
                ResponseWidget.createCorrectWidgets(widget);
            }else{
                instructionMgr.appendInstruction(widget.element, __('Please define the correct response below.'));
                ResponseWidget.createCorrectWidgets(widget);
            }

            commonRenderer.render(interaction);

            widget.$container.find('table.matrix input[type=checkbox]')
                .removeProp('disabled')
                .attr('data-edit', 'correct')
                .attr('data-role', 'correct');
        },
        setResponse : function(interaction, response){

            commonRenderer.setResponse(interaction, ResponseWidget.formatResponse(response));
        },
        destroy : function(widget){

            var interaction = widget.element;

            interaction.removeData('responseMappingMode');

            widget.$container.find('table.matrix input[type=checkbox]').prop('disabled', 'disabled');
            widget.$container.find('table.matrix .score').remove();
            widget.$container.off('responseChange.qti-widget');

            commonRenderer.resetResponse(interaction);
            commonRenderer.destroy(interaction);
        },
        createScoreWidgets : function(widget){

            var $container = widget.$container,
                interaction = widget.element,
                response = interaction.getResponseDeclaration(),
                mapEntries = response.getMapEntries(),
                defaultValue = response.getMappingAttribute('defaultValue');

            $container.find('table.matrix td>label').each(function(){

                var pairId = commonRenderer.inferValue(this).join(' ');

                $(this).append(scoreTpl({
                    serial : interaction.getSerial(),
                    choiceIdentifier : pairId,
                    score : mapEntries[pairId] ? mapEntries[pairId] : '',
                    placeholder : defaultValue
                }));
            });

            //add placeholder text to show the default value
            var $scores = $container.find('table.matrix .score');
            $scores.on('click', function(e){
                e.stopPropagation();
                e.preventDefault();
            });

            widget.on('mappingAttributeChange', function(data){
                if(data.key === 'defaultValue'){
                    $scores.attr('placeholder', data.value);
                }
            });

            formElement.setChangeCallbacks($container, response, {
                score : function(response, value){

                    var key = $(this).data('for');

                    if(value === ''){
                        response.removeMapEntry(key);
                    }else{
                        response.setMapEntry(key, value, true);
                    }

                }
            });

        },
        createCorrectWidgets : function(widget){

            var interaction = widget.element,
                response = interaction.getResponseDeclaration();

            widget.$container.on('responseChange.qti-widget', function(e, data){
                response.setCorrect(ResponseWidget.unformatResponse(data.response));
            });

        },
        formatResponse : function(response){

            var formatedRes = {list : {directedPair : []}};
            if(_.size(response) === 1){
                var pair = _.values(response).pop().split(' ');
                formatedRes = {base : {directedPair : pair}};
            }else{
                formatedRes = {list : {directedPair : []}};
                _.each(response, function(pairString){
                    var pair = pairString.split(' ');
                    formatedRes.list.directedPair.push(pair);
                });
            }
            return formatedRes;
        },
        unformatResponse : function(formatedResponse){

            var res = [];

            if(formatedResponse.list && formatedResponse.list.directedPair){
                _.each(formatedResponse.list.directedPair, function(pair){
                    res.push(pair.join(' '));
                });
            }else if(formatedResponse.base && formatedResponse.base.directedPair){
                res.push(formatedResponse.base.directedPair.join(' '));
            }
            return res;
        }
    };

    return ResponseWidget;
});
