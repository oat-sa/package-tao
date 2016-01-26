define([
    'lodash',
    'handlebars',
    'tpl!taoQtiItem/qtiXmlRenderer/tpl/interactions/portableCustomInteraction/properties'
], function(_, Handlebars, propertiesTpl){
    
    function renderPortableElementProperties(properties, ns, name){

        var entries = [];

        _.forIn(properties, function(value, key){
            if(_.isObject(value)){
                entries.push({
                    value : renderPortableElementProperties(value, ns, key)
                });
            }else{
                entries.push({
                    key : key,
                    value : value
                });
            }
        });
        
        return propertiesTpl({
            entries : entries,
            ns : ns,
            key : name
        });
    }

    //register the pci properties helper:
    Handlebars.registerHelper('portableElementProperties', function(properties, ns){
        return renderPortableElementProperties(properties, ns, '');
    });
    
});

