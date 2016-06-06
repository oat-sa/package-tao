define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/outcomeDeclaration'], function(_, tpl){
    return {
        qtiClass : 'outcomeDeclaration',
        template : tpl,
        getData:function(outcomeDeclaration, data){
            var defaultValue = [];
            if(outcomeDeclaration.defaultValue){
                if(typeof(outcomeDeclaration.defaultValue) === 'object'){
                    defaultValue = _.values(outcomeDeclaration.defaultValue);
                }else{
                    defaultValue = [outcomeDeclaration.defaultValue];
                }
            }
            
            var defaultData = {
                empty: !_.size(defaultValue),
                defaultValue: defaultValue
            };
            
            return _.merge(data || {}, defaultData);
        }
    };
});