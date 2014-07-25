define(['taoQtiItem/qtiCreator/widgets/Widget'], function(Widget){

    var ChoiceWidget = Widget.clone();

    ChoiceWidget.buildContainer = function(){
        this.$container = this.$original;
    };

    ChoiceWidget.initCreator = function(){

        Widget.initCreator.call(this);

        this.interaction = this.element.getInteraction();
        if(!this.interaction){
            throw new Error('cannot find related interaction');
        }

        this.listenToInteractionStates();
    };

    ChoiceWidget.listenToInteractionStates = function(){

        var _this = this;

        //follow interaction state change
        this.beforeStateInit(function(e, element, state){
            
            if(element.is('interaction')
                && element.getSerial() === _this.interaction.getSerial()
                && state.name !== _this.getCurrentState().name){
                
                switch(state.name){
                    case 'answer':
                    case 'sleep':
                    case 'question':
                        _this.changeState(state.name);
                        break;
                    case 'delete':
                        _this.changeState('sleep');
                        break;
                }

            }
        });
        
        this.beforeStateExit(function(e, element, state){
            if(element.is('interaction')
                && element.getSerial() === _this.interaction.getSerial()
                && state.name === 'choice'){
                
                _this.changeState('question');
            }
            
        });
    };

    return ChoiceWidget;
});
