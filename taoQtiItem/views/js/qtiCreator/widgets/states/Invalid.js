define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/invalidInfoBox'
], function($, stateFactory, invalidInfoBoxTpl){

    return stateFactory.create('invalid', ['sleep'], function(){

        this.widget.$container.addClass('invalid');

        this.$messageBox = $(invalidInfoBoxTpl({
            serial : this.widget.serial
        }));
         $('body').append(this.$messageBox);

    },function(){

        this.widget.$container.removeClass('invalid');
        this.$messageBox.remove();
    });
});
