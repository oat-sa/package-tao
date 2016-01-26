define([
    'taoQtiItem/qtiCreator/widgets/interactions/Widget',
    'taoQtiItem/qtiCreator/editor/customInteractionRegistry',
    'lodash'
], function(Widget, ciRegistry, _){

    var CustomInteractionWidget = Widget.clone();

    CustomInteractionWidget.initCreator = function(){

        //note : abstract widget class must not register states

        Widget.initCreator.call(this);
    };

    CustomInteractionWidget.createToolbar = function(options){

        var creator = ciRegistry.get(this.element.typeIdentifier);
        options = _.defaults(options || {}, {title : creator.manifest.label});

        return Widget.createToolbar.call(this, options);
    };

    return CustomInteractionWidget;
});