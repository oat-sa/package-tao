define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Custom',
    'tpl!taoQtiItem/qtiCreator/tpl/notifications/widgetOverlay',
    'i18n'
], function(stateFactory, Custom, overlayTpl, __){

    var InteractionStateCustom = stateFactory.create(Custom, function(){
        //use default [data-edit="custom"].show();
        this.widget.$container.append(overlayTpl({
            message : __('Custom Response Processing Mode')
        }));
        var $e = this.widget.$container.find('[data-edit=map], [data-edit=correct]').hide();
        
        //ok button z-index
    }, function(){
        //use default [data-edit="custom"].hide();
        this.widget.$container.children('.overlay').remove();
    });

    return InteractionStateCustom;
});
