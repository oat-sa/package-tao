define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/graphicInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'graphicAssociateInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});