define([
    'lodash',
    'jquery',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/customInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/runtime/qtiCustomInteractionContext'
], function(_, $, tpl, Helper, qtiCustomInteractionContext){

    var _getPci = function(interaction){

        var pciTypeIdentifier,
            pci = interaction.data('pci') || undefined;

        if(!pci){
            
            pciTypeIdentifier = interaction.getTypeIdentifier();
            pci = qtiCustomInteractionContext.getPci(pciTypeIdentifier);
            
            //two-way binding for pci hook and tao interaction
            interaction.data('pci', pci);
            pci._taoCustomInteraction = interaction;
            
            if(!pci){
                throw 'custome interaction node found for the type ' + pciTypeIdentifier;
            }
        }

        return pci;
    };

    var render = function(interaction){

        //get pci id
        var id = interaction.attr('id');
        var $dom = Helper.getContainer(interaction).find('#' + id);

        //get initialization params :
        var state = null,
            response = null,
            config = {},
            scripts = [],
            pci = _getPci(interaction);

        //script loading (issues)

        //call pci initialize();
        pci.initialize(id, $dom[0], config, state, response);

    };

    var setResponse = function(interaction, response){

        _getPci(interaction).setResponse(response);
    };

    var getResponse = function(interaction){

        _getPci(interaction).getResponse();
    };

    var resetResponse = function(interaction){

        _getPci(interaction).resetResponse();
    };

    var destroy = function(interaction){

        _getPci(interaction).destroy();
    };

    var getSerializedState = function(interaction){

        _getPci(interaction).getSerializedState();
    };

    return {
        qtiClass : 'customInteraction',
        template : tpl,
        render : render,
        getContainer : Helper.getContainer,
        setResponse : setResponse,
        getResponse : getResponse,
        resetResponse : resetResponse,
        destroy : destroy,
        getSerializedState : getSerializedState
    };
});