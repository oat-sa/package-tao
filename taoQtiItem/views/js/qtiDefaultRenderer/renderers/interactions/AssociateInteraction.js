define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/associateInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'associateInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});