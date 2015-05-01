define([
    'lodash',
    'jquery',
    'taoQtiItem/qtiCreator/widgets/Widget',
    'taoQtiItem/qtiCreator/widgets/helpers/movable',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/interaction',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/okButton'
], function(_, $, Widget, movable, toolbarTpl, okButtonTpl){

    /**
     * 
     * Create a new widget definition from another prototype.
     */
    var InteractionWidget = Widget.clone();

    /**
     * Optional method to be implemented :
     * Init the widget
     * It should never be called directly in normal usage
     * Here, it is overwriten to accomodate for a new argument 
     * that other widgets does not have: $responseForm
     */
    InteractionWidget.init = function(element, $container, $form, $responseForm, options){
        Widget.init.call(this, element, $container, $form, options);
        this.$responseForm = $responseForm;
        return this;
    };

    /**
     * Optional method to be implemented :
     * Build the widget and return an instance ready to be used
     * Here, it is overwritten to accomodate for a new argument
     * that other widgets does not have: $responseForm
     */
    InteractionWidget.build = function(element, $container, $form, $responseForm, options){
        return this.clone().init(element, $container, $form, $responseForm, options);
    };

    /**
     * Required method to be implemented : 
     * define the states and common structure valid for all states
     */
    InteractionWidget.initCreator = function(){
        
        Widget.initCreator.call(this);
        
        this.createToolbar({});
        this.createOkButton();
        this.listenToChoiceStates();
    };

    /**
     * Required method to be implemented : 
     * Define the contaieinr where everything is going on. 
     * It normally used this.$original as the start point : from there, you can wrap, innerWrap
     */
    InteractionWidget.buildContainer = function(){

        var $wrap = $('<div>', {
            'data-serial' : this.element.serial,
            'class' : 'widget-box widget-blockInteraction clearfix',
            'data-qti-class' : this.element.qtiClass
        });
        var $interactionContainer = this.$original.wrap($wrap);
        this.$container = $interactionContainer.parent();

        //@todo : implement movable interaction here:
//        movable.create(this);

        return this;
    };

    function _convertToTitle(str){
        str = str.replace(/[A-Z]/g, function(match){
            return ' ' + match.toUpperCase();
        });
        return str.charAt(0).toUpperCase() + str.substr(1);
    }

    /**
     * Below here, optional ui component init functions
     */

    /**
     * Create a toolbar
     */
    InteractionWidget.createToolbar = function(options){

        options = _.defaults(options || {}, {
            title : _convertToTitle(this.element.qtiClass)
        });

        var _this = this,
            $toolbar;

        $toolbar = $(toolbarTpl({
            title : options.title,
            serial : this.element.serial,
            switcher : !!this.registeredStates.answer
        }));

        this.$container.append($toolbar);
        $toolbar.hide();

        $toolbar.find('[data-role="delete"]').click(function(e){
            e.stopPropagation();//prevent direct deleting
            _this.changeState('deleting');
        });

        //if the answer state has been registered
        if(this.registeredStates.answer){

            //initialize the state switcher
            $toolbar.on('click', '.link', function(){
                var $link = $(this),
                    state = $link.data('state');

                $link.siblings('.selected').removeClass('selected').addClass('link');
                $link.removeClass('link').addClass('selected');
                _this.changeState(state);
            });

            //add stateChange event listener to auto toggle the question/answer trigger
            this.beforeStateInit(function(e, element, state){
                if(element.getSerial() === _this.serial){
                    var $link = $toolbar.find('.link[data-state="' + state.name + '"]');
                    if($link.length){
                        //a known active state:
                        $link.siblings('.selected').removeClass('selected').addClass('link');
                        $link.removeClass('link').addClass('selected');
                    }
                }
            });
        }

        return this;

    };

    InteractionWidget.createOkButton = function(){

        var _this = this,
            $ok = $(okButtonTpl()).on('click.qti-widget', function(e){
            e.stopPropagation();
            _this.changeState('sleep');
        });

        this.$container.append($ok);
    };

    InteractionWidget.listenToChoiceStates = function(){

        var _this = this;

        this.afterStateInit(function(e, element, state){

            var currentState = _this.getCurrentState();

            if(element.is('choice')
                && _this.element.getChoice(element.getSerial())
                && currentState
                && state.name !== currentState.name){

                switch(state.name){
                    case 'choice':
                        _this.changeState(state.name);
                        break;
                }

            }
        });

    };

    return InteractionWidget;
});
