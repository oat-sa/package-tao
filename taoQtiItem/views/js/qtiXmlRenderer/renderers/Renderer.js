define(['taoQtiItem/qtiRunner/core/Renderer', 'taoQtiItem/qtiXmlRenderer/renderers/config'], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});