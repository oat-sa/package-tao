define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Object',
    'taoQtiItem/qtiCreator/widgets/static/object/Widget'
], function(_, Renderer, Widget){

    var CreatorObject = _.clone(Renderer);

    CreatorObject.render = function(object, options){
        
        //initial rendering:
        Renderer.render(object);
        
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return Widget.build(
            object,
            Renderer.getContainer(object),
            this.getOption('bodyElementOptionForm'),
            options
        );
    };

    return CreatorObject;
});