define(['tpl!taoQtiItem/qtiXmlRenderer/tpl/container'], function(tpl){
    
    var xhtmlEntities = function(html){
        //@todo : check other names entities
        return html.replace(/&nbsp;/g, '&#160;');
    };
    
    var xhtmlEncode = function(encodedStr){

        var returnValue = '';

        if(encodedStr){
            
            //<br...> are replaced by <br... />
            returnValue = encodedStr;
            returnValue = returnValue.replace(/<br([^>]*)?>/ig, '<br />');
            returnValue = returnValue.replace(/<hr([^>]*)?>/ig, '<hr />');

            //<img...> are replaced by <img... />
            returnValue = returnValue.replace(/(<img([^>]*)?\s?[^\/]>)+/ig,
                function($0, $1){
                    return $0.replace('>', ' />');
                });
        }

        return returnValue;
    };
    
    return {
        qtiClass : '_container',
        template : tpl,
        getData:function(container, data){
            
            data.body = xhtmlEntities(data.body);
            data.body = xhtmlEncode(data.body);
            
            return data;
        }
    };
});