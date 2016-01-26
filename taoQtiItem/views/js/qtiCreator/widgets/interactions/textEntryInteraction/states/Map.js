define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/states/Map',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/inlineInteraction/textEntry',
    'lodash'
], function($, stateFactory, Map, formElement, optionTpl, _){

    var _getRelatedTextKey = function($elt){
        return $elt.closest('tr').find('input[name=text]').val();
    };

    var TextEntryInteractionStateMap = stateFactory.create(Map, function(){
        
        this.initMapEntries();
        this.initFormCallbacks();
        this.hideCorrectInput();
        
    }, function(){
        
        this.widget.$container.off('.map');
        this.widget.$container.find('tr[data-edit=map]:not([data-add-option])').remove();
    });
    
    TextEntryInteractionStateMap.prototype.hideCorrectInput = function(){
        
        var $correct = this.widget.$container.find('tr[data-edit=correct]');
        
        this.widget.on('metaChange', function(data){
            if(data.key === 'defineCorrect'){
                $correct.hide();
            }
        });
        
        //@todo : fix this!
        $correct.hide();
        _.delay(function(){
            $correct.hide();
        }, 200);
        
    };
    
    TextEntryInteractionStateMap.prototype.initFormCallbacks = function(){

        var $container = this.widget.$container,
            response = this.widget.element.getResponseDeclaration();

        formElement.setChangeCallbacks($container, response, {
            text : _.throttle(function(response, value){

                var $text = $(this),
                    oldText = $text.data('old-text'),
                    $tr = $text.closest('tr'),
                    correct = !!$tr.find('input[name=correct]:checked').length,
                    score = parseInt($tr.find('.score').val());

                //@todo trigger validate score to check if is a valid score
                if(!isNaN(score)){
                    response.removeMapEntry(oldText, true);
                    response.setMapEntry(value, score);
                    $text.data('old-text', value);

                    if(correct){
                        response.setCorrect(value);
                    }
                }

            }, 600),
            correct : function(response){
                var text = _getRelatedTextKey($(this));
                response.setCorrect(text);
            },
            score : function(response, value){

                var text = _getRelatedTextKey($(this));
                var score = parseFloat(value);

                if(!isNaN(score)){
                    response.setMapEntry(text, score);
                }
            }
        });
    };

    TextEntryInteractionStateMap.prototype.initMapEntries = function(){

        var _this = this,
            response = this.widget.element.getResponseDeclaration(),
            correctValue = _.values(response.getCorrect()).pop(),
            $container = this.widget.$container,
            $addOption = $container.find('tr[data-add-option]');
        
        var appendOption = function(text, score){
            
            var $newOption = $(optionTpl({
                text : text || '',
                score : score || 0,
                correct : (text === correctValue)
            }));
            $newOption.show();

            $addOption.before($newOption);
            
            preventNullMapEntries();
        };
        
        var preventNullMapEntries = function(){
            var $deleteButtons = $container.find('tbody [data-role=delete-option]');
            if($deleteButtons.length === 1){
                $deleteButtons.css('visibility', 'hidden');
            }else{
                $deleteButtons.css('visibility', 'visible');
            }
        };
        
        if(!_.size(response.mapEntries)){
            response.mapEntries = {'' : 0};
        }
        
        _.forIn(response.mapEntries, function(score, text){
            appendOption(text, score);
        });

        $container.on('click.map', '[data-role=delete-option]', function(){

            //init delete:
            var $del = $(this);
            var text = _getRelatedTextKey($del);
            var correct = !!$del.closest('tr').find('input[name=correct]:checked').length;

            response.removeMapEntry(text, true);
            $del.closest('tr').remove();
            preventNullMapEntries();
            
            if(correct){
                response.resetCorrect();//remove correct
            }

        }).on('click.map', '.add-option', function(){

            //init add option button
            appendOption('', 0);

            _this.initFormCallbacks();
        });

    };

    return TextEntryInteractionStateMap;
});
