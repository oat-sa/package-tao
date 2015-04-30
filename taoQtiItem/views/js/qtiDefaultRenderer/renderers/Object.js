define(['tpl!taoQtiItem/qtiDefaultRenderer/tpl/object', 'taoQtiItem/qtiDefaultRenderer/widgets/Object'], function(tpl, QtiObject){
    return {
        qtiClass : 'object',
        template : tpl,
        render : function(obj, data){
            var context = {};
            var runtimeContext = this.getOption('runtimeContext');
            if(runtimeContext && typeof(runtimeContext.root_url) !== 'undefined'){
                context.pluginPath = runtimeContext.root_url + '/taoQtiItem/views/js/qtiDefaultRenderer/lib/mediaelement/';//@todo: replace by MediaElementPlayer.pluginPath when refactoring complete
            }
            obj.widget = new QtiObject(obj, context);
            obj.widget.render();
        }
    };
});