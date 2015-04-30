define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/customInteraction/states/states',
    'liquidsInteraction/creator/widget/states/Question',
    'liquidsInteraction/creator/widget/states/Answer',
    'liquidsInteraction/creator/widget/states/Correct'
], function(factory, states){
    return factory.createBundle(states, arguments, ['map']);
});