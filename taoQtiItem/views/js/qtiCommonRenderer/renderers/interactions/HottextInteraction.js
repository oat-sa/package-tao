define([
    'lodash',
    'jquery',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/hottextInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'i18n'
], function(_, $, tpl, Helper, pciResponse, __){
    'use strict';


    /**
     * 'pseudo-label' is technically a div that behaves like a label.
     * This allows the usage of block elements inside the fake label
     */
    var pseudoLabel = function(interaction){


        var setChoice = function($choice, interaction){
            var $inupt = $choice.find('input');
            
            if($inupt.prop('checked') || $inupt.hasClass('disabled')){
                $inupt.prop('checked', false);
            }else{
                var maxChoices = parseInt(interaction.attr('maxChoices'));
                var currentChoices = _.values(_getRawResponse(interaction)).length;
                
                if(currentChoices < maxChoices || maxChoices === 0){
                    $inupt.prop('checked', true);
                }
            }
            Helper.triggerResponseChangeEvent(interaction);
            Helper.validateInstructions(interaction, {choice : $choice});
        };

        Helper.getContainer(interaction).find('.hottext').on('click', function(e){
            e.preventDefault();
            setChoice($(this), interaction);
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
        
        //set up the constraints instructions
        Helper.minMaxChoiceInstructions(interaction, {
            min: interaction.attr('minChoices'),
            max: interaction.attr('maxChoices'),
            getResponse : _getRawResponse,
            onError : function(data){
                var $input, $choice, $icon;
                if(data.choice && data.choice.length){
                    $choice = data.choice.addClass('error');
                    $input  = $choice.find('input');
                    $icon   = $choice.find(' > label > span').addClass('error cross');


                    setTimeout(function(){
                        $input.prop('checked', false);
                        $choice.removeClass('error');
                        $icon.removeClass('error cross');
                    }, 350);
                }
            }
        }); 
    };

    var resetResponse = function(interaction){
        Helper.getContainer(interaction).find('input').prop('checked', false);
    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        var $container = Helper.getContainer(interaction);

        try{
            _.each(pciResponse.unserialize(response, interaction), function(identifier){
                $container.find('input[value=' + identifier + ']').prop('checked', true);
            });
            Helper.validateInstructions(interaction);
        }catch(e){
            throw new Error('wrong response format in argument : ' + e);
        }
    };

    var _getRawResponse = function(interaction){
        var values = [];
        Helper.getContainer(interaction).find('input:checked').each(function(){
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
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10333
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    /**
     * Clean interaction destroy
     * @param {Object} interaction
     */
    var destroy = function destroy(interaction){
        var $container = Helper.getContainer(interaction);

        //restore response
        resetResponse(interaction);

        //restore selected choices:
        $container.find('.hottext').off('click');
    };  
    

    return {
        qtiClass : 'hottextInteraction',
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy
    };
});
