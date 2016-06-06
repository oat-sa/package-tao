define(['tpl!taoQtiItem/qtiXmlRenderer/tpl/element', 'lodash'], function(tpl, _){
    return {
        qtiClass : 'rubricBlock',
        template : tpl,
        getData : function(rubricBlock, data){
            var newData = {
                view : data.attributes.view ? _.values(data.attributes.view).join(' ') : ''
            };
            return _.merge(data || {}, newData);
        }
    };
});