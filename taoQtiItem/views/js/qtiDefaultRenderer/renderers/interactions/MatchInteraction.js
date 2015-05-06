define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/matchInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'matchInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});