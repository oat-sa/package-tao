define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/Math',
    'taoQtiItem/qtiCreator/widgets/static/math/Widget',
    'taoQtiItem/qtiCreator/widgets/static/helpers/inline'
], function(_, Renderer, Widget, inlineHelper){

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