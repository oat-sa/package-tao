define(['taoQtiItem/qtiXmlRenderer/renderers/Renderer'], function(XmlRenderer){

    var _xmlRenderer = new XmlRenderer({shuffleChoices : false}).load();

    var _render = function(item){
        var xml = '';
        try{
            xml = item.render(_xmlRenderer);
        }catch(e){
//            console.log(e);
        }

        return xml;
    };

    return {
        render : _render
    };
});