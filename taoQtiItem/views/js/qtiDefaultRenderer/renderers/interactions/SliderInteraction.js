define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/graphicInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'sliderInteraction',
        template : tpl,
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});