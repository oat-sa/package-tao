define([
    'lodash',
    'jquery',
    'i18n',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/choiceInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse'
], function(_, $, __, tpl, Helper, pciResponse){

    /**
     * 'pseudo-label' is technically a div that behaves like a label.
     * This allows the usage of block elements inside the fake label
     */
    var pseudoLabel = function(interaction){

        var $container = Helper.getContainer(interaction);

        $container.off('.commonRenderer');

        $container.on('click.commonRenderer', '.qti-choice', function(e){

            e.preventDefault();
            e.stopPropagation();//required toherwise any tao scoped ,i/form initialization might prevent it from working

            var $box = $(this);
            var $radios = $box.find('input:radio').not('[disabled]').not('.disabled');
            var $checkboxes = $box.find('input:checkbox').not('[disabled]').not('.disabled');

            if($radios.length){
                $radios.not(':checked').prop('checked', true);
                $radios.trigger('change');
            }

            if($checkboxes.length){
                $checkboxes.prop('checked', !$checkboxes.prop('checked'));
                $checkboxes.trigger('change');
            }

            Helper.validateInstructions(interaction, {choice : $box});
            Helper.triggerResponseChangeEvent(interaction);

        });

    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     * 
     * @param {object} interaction
     */
    var render = function(interaction){
        pseudoLabel(interaction);
        _setInstructions(interaction);
    };

    var _setInstructions = function(interaction){

        var min = interaction.attr('minChoices'),
            max = interaction.attr('maxChoices'),
            choiceCount = _.size(interaction.getChoices()),
            minInstructionSet = false;

        //if maxChoice = 1, use the radio gorup behaviour
        //if maxChoice = 0, inifinite choice possible
        if(max > 1 && max < choiceCount){

            var highlightInvalidInput = function($choice){
                var $input = $choice.find('.real-label > input'),
                    $li = $choice.css('color', '#BA122B'),
                    $icon = $choice.find('.real-label > span').css('color', '#BA122B').addClass('cross error');

                setTimeout(function(){
                    $input.prop('checked', false);
                    $li.removeAttr('style');
                    $icon.removeAttr('style').removeClass('cross');
                    Helper.triggerResponseChangeEvent(interaction);
                }, 150);
            };

            if(max === min){
                minInstructionSet = true;
                var msg = __('You must select exactly') + ' ' + max + ' ' + __('choices');
                Helper.appendInstruction(interaction, msg, function(data){
                    if(_getRawResponse(interaction).length >= max){
                        this.setLevel('success');
                        if(this.checkState('fulfilled')){
                            this.update({
                                level : 'warning',
                                message : __('Maximum choices reached'),
                                timeout : 2000,
                                start : function(){
                                    highlightInvalidInput(data.choice);
                                },
                                stop : function(){
                                    this.update({level : 'success', message : msg});
                                }
                            });
                        }
                        this.setState('fulfilled');
                    }else{
                        this.reset();
                    }
                });
            }else if(max > min){
                Helper.appendInstruction(interaction, __('You can select maximum') + ' ' + max + ' ' + __('choices'), function(data){
                    if(_getRawResponse(interaction).length >= max){
                        this.setMessage(__('Maximum choices reached'));
                        if(this.checkState('fulfilled')){
                            this.update({
                                level : 'warning',
                                timeout : 2000,
                                start : function(){
                                    highlightInvalidInput(data.choice);
                                },
                                stop : function(){
                                    this.setLevel('info');
                                }
                            });
                        }
                        this.setState('fulfilled');
                    }else{
                        this.reset();
                    }
                });
            }
        }

        if(!minInstructionSet && min > 0 && min < choiceCount){
            Helper.appendInstruction(interaction, __('You must select at least') + ' ' + min + ' ' + __('choices'), function(){
                if(_getRawResponse(interaction).length >= min){
                    this.setLevel('success');
                }else{
                    this.reset();
                }
            });
        }
    };

    var resetResponse = function(interaction){
        Helper.getContainer(interaction).find('.real-label > input').prop('checked', false);
    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var $container = Helper.getContainer(interaction);

        try{
            _.each(pciResponse.unserialize(response, interaction), function(identifier){
                $container.find('.real-label > input[value=' + identifier + ']').prop('checked', true);
            });
            Helper.validateInstructions(interaction);
        }catch(e){
            throw new Error('wrong response format in argument : ' + e);
        }
    };

    var _getRawResponse = function(interaction){
        var values = [];
        Helper.getContainer(interaction).find('.real-label > input[name=response-' + interaction.getSerial() + ']:checked').each(function(){
            values.push($(this).val());
        });
        return values;
    };

    /**
     * Return the response of the rendered interaction
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10278
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    var getCustomData = function(interaction, data){
        return _.merge(data || {}, {
            horizontal : (interaction.attr('orientation') === 'horizontal')
        });
    };

    var destroy = function(interaction){

        //remove event
        $(document).off('.commonRenderer');
        Helper.getContainer(interaction).off('.commonRenderer');

        //destroy response
        resetResponse(interaction);

        //remove instructions
        Helper.removeInstructions(interaction);

    };

    return {
        qtiClass : 'choiceInteraction',
        template : tpl,
        getData : getCustomData,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy
    };
});
