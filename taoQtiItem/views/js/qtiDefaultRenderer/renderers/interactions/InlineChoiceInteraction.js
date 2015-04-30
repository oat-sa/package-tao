define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/inlineChoiceInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'inlineChoiceInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});