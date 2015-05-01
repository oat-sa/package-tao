define([
    'layout/actions/binder',
    'uri',
    'jquery',
    'context',
    'taoItems/preview/preview',
    'helpers'
], function(binder, uri, $, context, preview, helpers){

    binder.register('itemPreview', function itemPreview(actionContext){
        preview.init(helpers._url('forwardMe', 'ItemPreview', context.shownExtension, {uri : actionContext.id}));
        preview.show();
    });

});
