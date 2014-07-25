define(['taoQtiItem/qtiItem/core/choices/TextVariableChoice'], function(QtiTextVariableChoice){
    var QtiInlineChoice = QtiTextVariableChoice.extend({
        qtiClass : 'inlineChoice'
    });
    return QtiInlineChoice;
});


