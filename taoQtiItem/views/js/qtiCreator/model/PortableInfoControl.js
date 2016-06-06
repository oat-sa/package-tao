define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiCreator/model/helper/portableElement',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    'taoQtiItem/qtiItem/core/PortableInfoControl'
], function(_, editable, portableElement, icRegistry, PortableInfoControl){
    "use strict";
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, portableElement.getDefaultMethods(icRegistry));
    _.extend(methods, {
        getDefaultMarkupTemplateData : function(){
            return {};
        }
    });

    return PortableInfoControl.extend(methods);
});