define(['taoQtiItem/qtiRunner/core/Renderer', 'taoQtiItem/qtiCreator/renderers/config'], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});