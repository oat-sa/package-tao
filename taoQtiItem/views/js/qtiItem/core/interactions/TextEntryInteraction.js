define(['taoQtiItem/qtiItem/core/interactions/InlineInteraction'], function(InlineInteraction){
    var TextEntryInteraction = InlineInteraction.extend({
        'qtiClass' : 'textEntryInteraction'
    });
    return TextEntryInteraction;
});