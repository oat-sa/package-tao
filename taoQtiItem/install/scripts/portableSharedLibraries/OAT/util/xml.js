define(function(){
    
    return {
        addNamespace : function addMarkupNamespace(xml, ns){
            return ns ? xml.replace(/<(\/)?([^!\/:>]+)(\/)?>/g, '<$1' + ns + ':$2$3>') : xml;
        },
        removeNamespace : function removeNamespace(markup){
            return markup.replace(/<(\/)?(\w*):([^>]*)>/g, '<$1$3>');
        }
    };
});