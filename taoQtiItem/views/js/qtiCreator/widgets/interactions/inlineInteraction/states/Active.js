define([
    'jquery', 
    'lodash',
    'taoQtiItem/qtiCreator/widgets/states/factory', 
    'taoQtiItem/qtiCreator/widgets/interactions/states/Active'], 
function($, _, stateFactory, Active){

    var InteractionStateActive = stateFactory.extend(Active, function(){
        
        var _this = this;
        $(window).on('resize.active', _.throttle(function(){
            _this.positionWidget();
        }, 200));
        
        this.positionWidget();

    }, function(){

        //unbind events
        $(window).off('.active');
        
        //hide it:
        this.widget.$container.hide();
    });

    InteractionStateActive.prototype.positionWidget = function(){
        
        //show toolbar, ok button, event binding performed by parent state
        var _widget = this.widget,
            itemOffset = _widget.$itemContainer.offset(),
            originalOffset = _widget.$original.offset();

        //debug
//        _widget.$itemContainer.on('mousemove', _.throttle(function(e){
//            console.log(e.pageX - itemOffset.left, e.pageY - itemOffset.top);
//        }, 200));
//        _widget.$itemContainer.css({border:'2px solid red'});
//        _widget.$container.css({border:'2px solid red'});
//        _widget.$original.css({border:'2px solid red'});

        //calculate absolute position:
        _widget.$container.show().css({
            position : 'absolute',
            top : originalOffset.top - itemOffset.top - 22,
            left : originalOffset.left - itemOffset.left - 32
        });
        
    };

    return InteractionStateActive;
});
