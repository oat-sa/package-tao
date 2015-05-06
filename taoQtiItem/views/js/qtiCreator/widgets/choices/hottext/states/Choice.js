define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/choices/states/Choice',
    'taoQtiItem/qtiCreator/widgets/choices/simpleChoice/states/Choice'
], function(stateFactory, Choice, SimpleChoice){

    var HottextStateChoice = stateFactory.extend(Choice);

    HottextStateChoice.prototype.initForm = function(){
        SimpleChoice.prototype.initForm.call(this);
    };

    return HottextStateChoice;
});