define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/responseDeclaration'], function(_, tpl){
    return {
        qtiClass : 'responseDeclaration',
        template : tpl,
        getData : function(responseDeclaration, data){
            var defaultData = {
                empty : !_.size(responseDeclaration.mapEntries) && !_.size(responseDeclaration.correctResponse) && !_.size(responseDeclaration.defaultValue),
                correctResponse : _.values(responseDeclaration.correctResponse),
                isAreaMapping : (responseDeclaration.attributes.baseType === "point"),
                mappingAttributes : responseDeclaration.mappingAttributes,
                hasMapEntries : _.size(responseDeclaration.mapEntries),
                mapEntries : responseDeclaration.mapEntries,
                defaultValue : responseDeclaration.defaultValue,
                isRecord : responseDeclaration.attributes.cardinality === 'record'
            };

            return _.merge(defaultData, data || {});
        }
    };
});