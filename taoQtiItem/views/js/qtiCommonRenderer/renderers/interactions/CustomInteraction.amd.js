define([
    'lodash',
    'tpl!taoQtiItem/qtiCommonRenderer/tpl/interactions/customInteraction',
    'taoQtiItem/qtiCommonRenderer/helpers/Helper',
    'taoQtiItem/runtime/qtiCustomInteractionContext',
    'taoQtiItem/qtiItem/helper/util',
    'context'
], function(_, tpl, Helper, qtiCustomInteractionContext, util, context){

    var _registerGlobalPciContext = function(){

        window.qtiCustomInteractionContext = window.qtiCustomInteractionContext || qtiCustomInteractionContext;
    };

    var _registerLibraries = function(paths){

        window.require.config({
            context : 'portableCustomInteraction',
            paths : paths
        });
    };

    var _pciRequire = function(modules, callback){

        var pciReq = require.config({context : 'portableCustomInteraction'});
        pciReq(modules, callback);
    };

    var _getPci = function(interaction){

        var pciTypeIdentifier,
            pci = interaction.data('pci') || undefined;

        if(!pci){

            pciTypeIdentifier = interaction.typeIdentifier;
            pci = qtiCustomInteractionContext.getPci(pciTypeIdentifier);

            if(pci){

                //two-way binding for pci hook and tao interaction
                interaction.data('pci', pci);
                pci._taoCustomInteraction = interaction;

            }else{
                throw 'no custom interaction hook found for the type ' + pciTypeIdentifier;
            }
        }

        return pci;
    };

    var _getLibraries = function(interaction, baseUrl){

        var libraries = interaction.libraries || [],
            ret = [],
            paths = {};
        
        _.forIn(libraries, function(href, name){

            var hrefFull = util.fullpath(href, baseUrl);

            if(/\.js$/.test(hrefFull)){
                paths[name] = hrefFull.replace(/\.js$/, '');
                ret.push(name);
            }else if(/\.css$/.test(hrefFull)){
                paths[name] = hrefFull.replace(/\.css$/, '');
                ret.push('css!' + name);
            }

        });

        //register:
        _registerLibraries(paths);

        return ret;
    };
    
    var render = function(interaction){

        _registerGlobalPciContext();
        _registerLibraries({
            css : context.root_url + 'tao/views/js/lib/require-css/css'
        });

        //get pci id
        var id = interaction.attr('responseIdentifier');
        var $dom = Helper.getContainer(interaction).find('#' + id);

        //get initialization params :
        var state = null,
            response = null,
            config = interaction.properties,
            libraries = _getLibraries(interaction, this.getOption('baseUrl'));

        //libraries loading (issues)
        _pciRequire(libraries, function(){

            var pci = _getPci(interaction);
            if(pci){
                //call pci initialize();
                pci.initialize(id, $dom[0], config, state, response);
            }
            
        });
        
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