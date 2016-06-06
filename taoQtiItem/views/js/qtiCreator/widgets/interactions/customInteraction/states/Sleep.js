define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/states/Sleep',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/widgetOverlay'
], function(stateFactory, SleepState, overlayTpl){

    return stateFactory.extend(SleepState, function(){

        //add transparent protective layer
        this.widget.$container.append(overlayTpl());

    }, function(){

        //remove transparent protective layer
        this.widget.$container.children('.overlay').remove();
    });
});
