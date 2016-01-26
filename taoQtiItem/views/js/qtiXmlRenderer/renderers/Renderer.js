define(['taoQtiItem/qtiRunner/core/Renderer', 'taoQtiItem/qtiXmlRenderer/renderers/config'], function(Renderer, config){
    'use strict';

    return Renderer.build(config.locations, config.name, config.options);
});
