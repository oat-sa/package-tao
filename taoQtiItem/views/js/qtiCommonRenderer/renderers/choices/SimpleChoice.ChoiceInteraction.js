define(['tpl!taoQtiItem/qtiCommonRenderer/tpl/choices/simpleChoice.choiceInteraction', 'taoQtiItem/qtiCommonRenderer/helpers/Helper'], function(tpl, Helper){
    return {
        qtiClass : 'simpleChoice.choiceInteraction',
        getContainer : Helper.getContainer,
        getData:function(choice, data){
            data.unique = (parseInt(data.interaction.attributes.maxChoices) === 1);
            return data;
        },
        template : tpl
    };
});