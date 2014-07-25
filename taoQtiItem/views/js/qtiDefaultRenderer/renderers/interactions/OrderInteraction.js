define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/interactions/choiceInteraction', 'taoQtiItem/qtiDefaultRenderer/renderers/interactions/Interaction'], function(tpl, Interaction){
    return {
        qtiClass : 'orderInteraction',
        template : tpl,//reuse choiceInteraction template
        render : Interaction.render,
        setResponse : Interaction.setResponse,
        getResponse : Interaction.getResponse
    };
});