define(['lodash', 'tpl!taoQtiItem/qtiXmlRenderer/tpl/responseDeclaration'], function(_, tpl){
    return {
        qtiClass : 'responseDeclaration',
        template : tpl,
        getData : function(responseDeclaration, data){
            var defaultData = {
                empty : !_.size(responseDeclaration.mapEntries) && !_.size(responseDeclaration.correctResponse) && !_.size(responseDeclaration.defaultValue),
                correctResponse : _.values(responseDeclaration.correctResponse),
                MATCH_CORRECT : (responseDeclaration.template === "http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct"),
                MAP_RESPONSE : (responseDeclaration.template === "http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response"),
                MAP_RESPONSE_POINT : (responseDeclaration.template === "http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point"),
                mappingAttributes : responseDeclaration.mappingAttributes,
                mapEntries : responseDeclaration.mapEntries
            };
            return _.merge(defaultData, data || {});
        }
    };
});