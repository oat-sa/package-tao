define([
    'jquery',
    'taoQtiItem/qtiCreator/widgets/static/Widget',
    'taoQtiItem/qtiCreator/widgets/static/rubricBlock/states/states',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/textBlock'
], function($, Widget, states, toolbarTpl) {

    var RubricBlockWidget = Widget.clone();

    RubricBlockWidget.buildContainer = function() {
        this.$container = this.$original.addClass('widget-box widget-rubricBlock');
    };

    RubricBlockWidget.initCreator = function() {

        this.registerStates(states);

        Widget.initCreator.call(this);
    };

    RubricBlockWidget.createToolbar = function() {

        var $tlb = $(toolbarTpl({
                serial: this.serial,
                state: 'active'
            })),
            _this = this;

        this.$container.find('.qti-rubricBlock-body').append($tlb);

        $tlb.find('[data-role="delete"]').on('click.widget-box', function(e){
            e.stopPropagation();//to prevent direct deleting;
            _this.changeState('deleting');
        });

        return this;
    };

    return RubricBlockWidget;
});
