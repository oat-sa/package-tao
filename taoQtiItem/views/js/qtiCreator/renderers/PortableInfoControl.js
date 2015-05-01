define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/PortableInfoControl',
    'taoQtiItem/qtiCreator/editor/infoControlRegistry',
    'taoQtiItem/qtiCreator/helper/commonRenderer'
], function(_, Renderer, icRegistry, commonRenderer){

    var CreatorPortableInfoControl = _.clone(Renderer);

    CreatorPortableInfoControl.render = function(infoControl, options){

        //initial rendering:
        Renderer.render.call(commonRenderer.get(), infoControl, {baseUrl : icRegistry.getBaseUrl(infoControl.typeIdentifier)});

        var pciCreator = icRegistry.getCreator(infoControl.typeIdentifier),
            Widget = pciCreator.getWidget();

        return Widget.build(
            infoControl,
            Renderer.getContainer(infoControl),
            this.getOption('bodyElementOptionForm'),
            options
            );
    };

    return CreatorPortableInfoControl;
});