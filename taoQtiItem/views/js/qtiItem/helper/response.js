define(['lodash'], function(_){
    
   var _templateNames = {
        'MATCH_CORRECT' : 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct',
        'MAP_RESPONSE' : 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response',
        'MAP_RESPONSE_POINT' : 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point'
    };
    
   return {
       isUsingTemplate : function(response, tpl){
            if(_.isString(tpl)){
                if(tpl === response.template || _templateNames[tpl] === response.template){
                    return true;
                }
            }
            return false;
        },
        isValidTemplateName:function(tplName){
            return !!this.getTemplateUriFromName(tplName);
        },
        getTemplateUriFromName:function(tplName){
            if(_templateNames[tplName]){
                return _templateNames[tplName];
            }
            return '';
        },
        getTemplateNameFromUri:function(tplUri){
            var tplName = '';
            _.forIn(_templateNames, function(uri, name){
                if(uri === tplUri){
                    tplName = name;
                    return false;
                }
            });
            return tplName;
        }
   };
});