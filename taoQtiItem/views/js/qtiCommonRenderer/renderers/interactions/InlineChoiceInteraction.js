define([
    'lodash',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/inlineChoiceInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/qtiCommonRenderer/helpers/PciResponse',
    'i18n',
    'select2',
    'tooltipster'
], function(_, tpl, Helper, pciResponse, __){

    /**
     * The value of the "empty" option
     * @type String
     */
    var _emptyValue = 'empty';

    var _defaultOptions = {
        allowEmpty : true,
        placeholderText : __('select a choice')
    };

    /**
     * Init rendering, called after template injected into the DOM
     * All options are listed in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     * 
     * @param {object} interaction
     */
    var render = function(interaction, options){

        var opts = _.clone(_defaultOptions),
            required = !!interaction.attr('required');
        _.extend(opts, options);

        var $container = Helper.getContainer(interaction);

        if(opts.allowEmpty && !required){
            $container.find('option[value=' + _emptyValue + ']').text('--- ' + __('leave empty') + ' ---');
        }else{
            $container.find('option[value=' + _emptyValue + ']').remove();
        }

        $container.select2({
            width : 'resolve',
            placeholder : opts.placeholderText,
            minimumResultsForSearch : -1
        });

        var $el = $container.select2('container');

        _setInstructions(interaction);

        $container.on('change', function(){

            if(required && $container.val() !== ""){
                $el.tooltipster('hide');
            }

            Helper.triggerResponseChangeEvent(interaction);

        });

        if(required){
            
            $container.on('select2-open', function(){

                $el.tooltipster('hide');

            }).on('select2-close', function(){

                if($container.val() === ""){
                    $el.tooltipster('show');
                }
            });
            
        }
    };

    var _setInstructions = function(interaction){

        var required = !!interaction.attr('required'),
            $container = interaction.getContainer(),
            $el = $container.select2('container');

        if(required){
            //set up the tooltip plugin for the input
            $el.tooltipster({
                theme : 'tao-warning-tooltip',
                content : __('A choice must be selected'),
                delay : 250,
                trigger : 'custom'
            });

            if($container.val() === ""){
                $el.tooltipster('show');
            }
        }

    };

    var resetResponse = function(interaction){
        _setVal(interaction, _emptyValue);
    };

    var _setVal = function(interaction, choiceIdentifier){

        Helper.getContainer(interaction)
            .val(choiceIdentifier)
            .select2('val', choiceIdentifier)
            .change();

    };

    /**
     * Set the response to the rendered interaction.
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     * 
     * @param {object} interaction
     * @param {object} response
     */
    var setResponse = function(interaction, response){

        _setVal(interaction, pciResponse.unserialize(response, interaction)[0]);
    };

    var _getRawResponse = function(interaction){
        var value = Helper.getContainer(interaction).val();
        return (value && value !== _emptyValue) ? [value] : [];
    };

    /**
     * Return the response of the rendered interaction
     * 
     * The response format follows the IMS PCI recommendation :
     * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343  
     * 
     * Available base types are defined in the QTI v2.1 information model:
     * http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10321
     * 
     * @param {object} interaction
     * @returns {object}
     */
    var getResponse = function(interaction){
        return pciResponse.serialize(_getRawResponse(interaction), interaction);
    };

    return {
        qtiClass : 'inlineChoiceInteraction',
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse
    };
});