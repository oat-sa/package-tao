/**
 * Define the Qti Item Common Renderer
 * 
 * @todo remove css!tao_css/tao-main-style in next major release (it has been moved to qtiLoader.js for better rendering result). It is kept here for previous version compability
 */
define([
    'taoQtiItem/qtiRunner/core/Renderer',
    'taoQtiItem/qtiCommonRenderer/renderers/config',
    'css!taoQtiItem_css/qti',
    'css!tao_css/tao-main-style'
], function(Renderer, config){
    return Renderer.build(config.locations, config.name);
});