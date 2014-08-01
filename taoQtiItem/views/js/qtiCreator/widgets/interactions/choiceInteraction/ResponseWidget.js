define([
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/ChoiceInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/answerState',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/simpleChoice.score',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/simpleChoice.label',
    'lodash',
    'i18n',
    'polyfill/placeholders'
], function(commonRenderer, helper, pciResponse, formElement, answerStateHelper, scoreTpl, labelTpl, _, __){

    var _fixInputs = function(widget){

        if(widget.element.attr('maxChoices') === 1){
            //enforce radios:
            widget.$container.find('.real-label > :checkbox').replaceWith(function(){

                var $checkbox = $(this);

                return $('<input>', {
                    type : 'radio',
                    value : $checkbox.attr('value'),
                    name : $checkbox.attr('name')
                });

            });

        }else{

            //enforce radios:
            widget.$container.find('.real-label > :radio').replaceWith(function(){

                var $radio = $(this);

                return $('<input>', {
                    type : 'checkbox',
                    value : $radio.attr('value'),
                    name : $radio.attr('name')
                });

            });
        }

    };

    var ResponseWidget = {
        create : function(widget, responseMappingMode){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            _fixInputs(widget);

            if(responseMappingMode){
                helper.appendInstruction(widget.element, __('Please define the correct response and the score below.'));
                interaction.data('responseMappingMode', true);
                ResponseWidget.createScoreWidgets(widget);
                ResponseWidget.createCorrectWidgets(widget);
            }else{
                helper.appendInstruction(widget.element, __('Please define the correct response below.'));
                ResponseWidget.createCorrectWidgets(widget);
            }

            commonRenderer.render(interaction);

        },
        setResponse : function(interaction, response){

            commonRenderer.setResponse(interaction, pciResponse.serialize(_.values(response), interaction));
        },
        destroy : function(widget){

            var interaction = widget.element;

            commonRenderer.destroy(interaction);

            interaction.removeData('responseMappingMode');

            widget.$container.off('responseChange.qti-widget');
            
            widget.$container.find('.real-label > input').attr('disabled', 'disabled');
            
            widget.$container.find('.mini-tlb-label[data-edit=answer], .mini-tlb[data-edit=answer]').remove();
        },
        createScoreWidgets : function(widget){

            var $container = widget.$container,
                interaction = widget.element,
                response = interaction.getResponseDeclaration(),
                mapEntries = response.getMapEntries(),
                defaultValue = response.getMappingAttribute('defaultValue');

            var $label = $(labelTpl({
                label : __('score'),
                show : true
            })).css({
                right : 3,
                left : 'auto'
            });

            $container.find('.qti-choice:first .pseudo-label-box').append($label);

            $container.find('.qti-choice').each(function(){

                var $choice = $(this),
                    id = $choice.data('identifier'),
                    serial = $choice.data('serial'),
                    $score;

                $score = $(scoreTpl({
                    serial : interaction.getSerial(),
                    choiceSerial : serial,
                    choiceIdentifier : id,
                    score : mapEntries[id] ? mapEntries[id] : '',
                    placeholder : defaultValue
                }));

                $choice.find('.pseudo-label-box').append($score);

                $score.show().on('click', function(e){
                    e.stopPropagation();
                    e.preventDefault();
                });

            });

            //add placeholder text to show the default value
            var $scores = $container.find('.score');
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

            var $container = widget.$container,
                interaction = widget.element,
                response = interaction.getResponseDeclaration(),
                $corrects = $container.find('.real-label > input');

            $container.find('.qti-choice:first .pseudo-label-box').append(labelTpl({
                label : __('correct'),
                show : false
            }));

            $container.on('responseChange.qti-widget', function(e, data){
                response.setCorrect(pciResponse.unserialize(data.response, interaction));
            });

            var _toggleCorrectInputs = function(show){
                if(show){
                    $corrects.removeAttr('disabled');
                }else{
                    $corrects.attr('disabled', 'disabled').prop('checked', false);
                }
            };
            
            _toggleCorrectInputs(answerStateHelper.defineCorrect(response));
            
            widget.on('metaChange', function(data){
                if(data.element.serial === response.serial && data.key === 'defineCorrect'){
                    _toggleCorrectInputs(data.value);
                }
            });

        },
        formatResponse : function(response){
            return pciResponse.serialize(_.values(response));
        },
        unformatResponse : function(formatedResponse){
            return pciResponse.unserialize(formatedResponse);
        }
    };

    return ResponseWidget;
});