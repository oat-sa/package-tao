/**
 * Define the Qti Item Common Renderer
 */
define([
    'taoQtiItem/qtiRunner/core/Renderer',
    'taoQtiItem/qtiCommonRenderer/renderers/config',
    'css!taoQtiItemCss/qti'
], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});
