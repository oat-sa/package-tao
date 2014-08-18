define([
    'taoQtiItem/qtiRunner/core/Renderer',
    'taoQtiItem/qtiDefaultRenderer/renderers/config',
    'css!taoQtiItem/../css/normalize.css',
    'css!taoQtiItem/../css/base.css',
    'css!taoQtiItem/qtiDefaultRenderer/css/qti.css'
], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});