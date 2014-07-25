define([
    'lodash',
    'taoQtiItem/qtiCommonRenderer/renderers/RubricBlock',
    'taoQtiItem/qtiCreator/widgets/static/rubricBlock/Widget'
], function(_, Renderer, Widget){

    var CreatorRubricBlock = _.clone(Renderer);

    CreatorRubricBlock.render = function(rubricBlock, options){
        
        return Widget.build(
            rubricBlock,
            Renderer.getContainer(rubricBlock),
            this.getOption('bodyElementOptionForm'),
            options
        );
    };

    return CreatorRubricBlock;
});