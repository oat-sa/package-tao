define(['taoQtiItem/qtiCreator/helper/adaptSize', 'lodash'], function(adaptSize, _){
    
    return {
        adaptSize : function(widget){
            _.defer(function(){
                adaptSize.height(widget.$container.find('.add-option, .result-area .target, .choice-area .qti-choice'));
            });
        }
    }
});