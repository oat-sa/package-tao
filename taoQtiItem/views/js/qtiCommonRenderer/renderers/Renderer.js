define([
    'taoQtiItem/qtiRunner/core/Renderer',
    'taoQtiItem/qtiCommonRenderer/renderers/config',
    'css!tao_css/tao-main-style',// not until fonts are separated
    'css!taoQtiItem_css/qti'
], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});
