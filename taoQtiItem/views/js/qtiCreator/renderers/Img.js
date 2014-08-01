define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Img',
    'taoQtiItem/qtiCreator/widgets/static/img/Widget'
], function(_, Renderer, Widget){

    var CreatorImg = _.clone(Renderer);

    CreatorImg.render = function(img, options){
        
        options = options || {};
        options.baseUrl = this.getOption('baseUrl');
        options.uri = this.getOption('uri');
        options.lang = this.getOption('lang');
        options.mediaManager = this.getOption('mediaManager');
        
        return Widget.build(
            img,
            Renderer.getContainer(img),
            this.getOption('bodyElementOptionForm'),
            options
        );
    };

    return CreatorImg;
});