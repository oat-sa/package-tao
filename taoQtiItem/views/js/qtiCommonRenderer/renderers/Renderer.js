define([
    'taoQtiItem/qtiRunner/core/Renderer',
    'taoQtiItem/qtiCommonRenderer/renderers/config',
    'css!taoQtiItem_css/qti'
], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});
