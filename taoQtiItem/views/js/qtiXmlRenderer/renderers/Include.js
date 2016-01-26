define(['tpl!taoQtiItem/qtiXmlRenderer/tpl/include'], function(tpl){
    return {
        qtiClass : 'include',
        template : tpl,
        getData : function(xi, data){

            var ns = xi.getNamespace();

            if(ns && ns.name){
                data.tag = ns.name + ':include';
            }

            return data;
        }
    };
});