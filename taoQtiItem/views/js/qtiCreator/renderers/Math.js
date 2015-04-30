define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Math',
    'taoQtiItem/qtiCreator/widgets/static/math/Widget'
], function(_, Renderer, Widget){

    var CreatorMath = _.clone(Renderer);

    CreatorMath.render = function(math, options){
        
        //initial rendering:
        Renderer.render(math);
        
        return Widget.build(
            math,
            Renderer.getContainer(math),
            this.getOption('bodyElementOptionForm'),
            options
        );
    };

    return CreatorMath;
});