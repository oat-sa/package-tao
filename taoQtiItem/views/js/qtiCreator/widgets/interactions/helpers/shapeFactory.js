/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash',
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/shapeHandlers',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/shapeResizer',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/shapeMover',
    'taoQtiItem/qtiCreator/widgets/interactions/helpers/pathBuilder'
], function($, _, graphicHelper, shapeHandlers, shapeResizer, shapeMover, pathBuilder){

    var shapeFactory = function shapeFactory(options){
        var paper = options.paper;
        var background = options.background;
        var $container = options.$container;
        var isResponsive = options.isResponsive || false;
        var type = options.type || 'rect';

        var creator = {
            
            states : {
                drawing : false
            },

            _events : {},

            on : function on(eventName, cb){
                if(_.isFunction(cb)){
                    this._events[eventName] = cb;
                }
            },

            is : function is(state){
                return this.states[state] === true;
            },
            
            setState : function setState(state, value){
                this.states[state] = value;
            },

            /**
             * Start the shape creation
             */ 
            start : function(){
                switch(type){
                    case 'path'     : this.startDrawingPath(); break;
                    case 'target'   : this.startPositionTarget(); break;
                    default         : this.startWithMouse(); break;
                }
            },

            startPositionTarget : function startPositionTarget(){
                var self = this;
                self.setState('drawing', true);

                background.click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
 
                    //get the current mouse point, even on a responsive paper
                    var point = graphicHelper.getPoint(e, paper, $container, isResponsive);
                    
                    //add the point to the paper
                    graphicHelper.createTarget(paper, {
                        point : point, 
                        create : function created(target){
                            self.setState('drawing', false);
                            background.unclick();
                            if(self._events['created.qti-widget']){
                               self._events['created.qti-widget'].call(this, target); 
                            }
                        },
                        remove : false,
                        hover  : false
                    });
                });
            },

            startDrawingPath : function startDrawingPath(){
                var self = this;
                var builder = pathBuilder(paper);
                
                self.setState('drawing', true);
                
                builder.onClose(created);
                background.click(function(e){
                    e.preventDefault();
                    e.stopPropagation();             
                    if(self.is('drawing')){
                        builder.add(
                            graphicHelper.getPoint(e, paper, $container, isResponsive)
                        );
                    }
                });

                function created(){
                    self.setState('drawing', false);
                    background.unclick();
                    if(self._events['created.qti-widget']){
                       self._events['created.qti-widget'].call(this, builder.getPath()); 
                    }
                }
            },
          
            startWithMouse : function startWithMouse(){            
                var self = this;
                var smoothResize = _.throttle(resize, 10);
                var startPoint;
                var shape;
                
                background.mousedown( function startDrawing(event){
                    event.preventDefault();

                    if(!self.is('drawing')){
                        self.setState('drawing', true);

                        startPoint = graphicHelper.getPoint(event, paper, $container, isResponsive);

                        //create a base shape
                        shape = graphicHelper.createElement(paper, type, [startPoint.x, startPoint.y, 25, 25], { 
                                style       : 'creator',
                                hover       : false, 
                                touchEffect : false, 
                                qtiCoords   : false
                            });
    
                        shape.mouseup(created);
                        background.mouseup(created);
                        
                        //resize it now
                        shape.mousemove(smoothResize);
                        background.mousemove(smoothResize);    
                    }
                });

                function created(){
                    self.setState('drawing', false);
                    background
                        .unmousedown()
                        .unmousemove()
                        .unmouseup();
                    shape
                        .unmousedown()
                        .unmouseup()
                        .unmousemove();
                    if(self._events['created.qti-widget']){
                       self._events['created.qti-widget'].call(this, shape); 
                    }
                }

                function resize(event){
                    if(self.is('drawing')){
                        shapeResizer(shape, {
                            start   : startPoint,
                            stop    : graphicHelper.getPoint(event, paper, $container, isResponsive)
                        });
                    }
                }
            },

            stop : function(){

                    background
                        .unmousedown()
                        .unmousemove()
                        .unmouseup()
                        .unclick();
            }
        };

        return creator;
    };

    return shapeFactory;
});
