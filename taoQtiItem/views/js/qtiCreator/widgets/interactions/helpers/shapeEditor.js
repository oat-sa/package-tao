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

    /**
     * Enables you to edit a shape: handling, resizing, moving.
     * This function is a factory that gives you a new instance of editor.
     * 
     * @exports taoQtiItem/qtiCreator/widgets/interactions/helpers/shapeEditor
     *
     * @param {Raphael.Element} shape - the shape to edit
     * @param {Object} options - the contextual options
     * @param {Raphael.Paper} options.paper - the raphael paper
     * @param {Raphael.Element} options.background - the paper's background image element
     * @param {jQueryElement} options.$container - the paper container
     * @param {Boolean} [options.isResponsive = false] - if we are in responsive mode (auto scalling)
     * @returns {shapeEditor.editor} the shape editor instance
     */
    var shapeEditor = function(shape, options){

        var paper = options.paper;
        var background = options.background;
        var $container = options.$container;
        var isResponsive = options.isResponsive || false;

        /**
         * The editor linked to a shape 
         * @type shapeEditor.editor
         */
        var editor = {

            shape : null,

            handlers : [],

            states : {
                drawing : false,
                handling : false,
                resizing : false,
                moving : false
            },

            _events : {},

            /**
             * Listen for an event bound to the editor
             * @param {String} eventName - the name of the event to listen
             * @param {Function} cb - event callback 
             * @returns {shapeEditor.editor} for chaining
             */
            on : function on(eventName, cb){
                if(_.isFunction(cb)){
                    this._events[eventName] = cb;
                }
                return this;
            },

            /**
             * Trigger an event on the editor
             * @param {String} eventName - the name of the event to trigger
             * @param {...*} args - additional args to pass to the bound callbacks
             * @returns {shapeEditor.editor} for chaining
             */
            trigger : function(eventName){
                if(_.isFunction(this._events[eventName])){
                    this._events[eventName].apply(this, Array.prototype.slice.call(arguments, 1));
                }
                return this;
            },

            /**
             * Check if the editor is in the given state
             * @param {String} state - the state name
             * @returns {Boolean} is in the state or not 
             */
            is : function is(state){
                return this.states[state] === true;
            },
            
            /**
             * Set the editor in a particular state
             * @param {String} state - the state name
             * @param {Boolean} value -
             * @returns {shapeEditor.editor} for chaining
             */
            setState : function setState(state, value){
                this.states[state] = value;
                return this;
            },


            /**
             * Set up the editor 
             * @constructor
             * @private 
             * @param {Raphael.Element} shape - the shape to edit
             * @returns {shapeEditor.editor} the shape editor instance
             */ 
            _init : function(shape){
                var self = this;
                if(shape && shape.type){
                    self.shape = shape;
                    background.click(function(){
                         if(!self.is('resizing') && self.is('handling')){
                            self.quitHandling();  
                         }
                    });
                    self.shape.click(function(){
                         if(!self.is('resizing')){
                            if(!self.is('handling')){
                                self.enterHandling();  
                            } else {
                                self.quitHandling();  
                            }    
                        }
                    });
                }
                return this;
            },

            /**
             * Start the shape handling 
             * @returns {shapeEditor.editor} the shape editor instance
             */ 
            enterHandling : function enterHandling(){
                var self = this;
                
                //enter handling
                self.setState('handling', true)
                    .trigger('enterhandling.qti-widget');
    
                //do not move set
                if(self.shape.type === 'set'){
                    return;
                }                   

                //create handlers to resize the shape
                self.handlers = shapeHandlers(paper, self.shape);
                _.forEach(self.handlers, function(handler){
                    //drag the handlers to resize the shape
                    handler.drag(resize, startResize, resized);
                });

                //drag the shape to move it
                self.shape.drag(move, startMove, moved);

                var keyResizer = _.throttle(keyResize, 20);
  
                //bind keys 
                $(document).on('keydown.qti-widget', function(e){
                    var code = e.which;
                    //arrows
                    if(code >= 37 && code <= 40){
                        e.preventDefault();
                        keyResizer(code);
                    }
                    //delete and backspace
                    //if(e.which === 8 || e.which === 46){
                        //e.preventDefault();
                        //self.quitHandling();
                        //self.removeShape();
                    //}
                });

                /**
                 * Start the resizing from an handler, create the layer, the start point, etc.
                 * Used as handler dragstart callback
                 * @private
                 */ 
                function startResize (){ 
                    var handler  = this; 
                    var bbox;
                    self.setState('resizing', true)
                        .trigger('shapechanging.qti-widget');
                    
                    //create a layer to be reiszed
                    self.layer = self.shape.clone();
                    self.layer.attr(graphicHelper._style.basic);
                    self.layer.attr('cursor', handler.attrs.cursor);

                    bbox = self.layer.getBBox();
                    self.layerTxt = graphicHelper.createShapeText(paper, self.layer, { 
                        style   : 'layer-pos-text',
                        content : parseInt(bbox.width, 10) + ' x ' + parseInt(bbox.height, 10) 
                    });

                    if(self.shape.type === 'path'){
                       _.forEach(self.shape.attr('path'), function(point, index){
                           if(point.length === 3 && point[1] === this.attr('cx') && point[2] === this.attr('cy')){
                                this.pointIndex = index;
                                return false; 
                           }                 
                       }, handler); 
                    }

                    //hide others
                    _.invoke(_.reject(_.clone(self.handlers), function(elt){
                        return elt === handler;
                    }), 'hide');                              
                }
                 
                /**
                 * Resize a shape (in fact only the layer)
                 * Used as handler drag callback
                 * @private
                 */ 
               function resize(dx, dy, x, y, event){
                    var stopPoint, options;
                    if(self.is('resizing')){

                        stopPoint = graphicHelper.getPoint(event, paper, $container, isResponsive);
                        options =  {
                            stop        : stopPoint,
                            constraints : this.data('constraints'),
                            resized     : _.throttle(function(){
                                var bbox = this.getBBox();
                                self.layerTxt.attr('text',  parseInt(bbox.width, 10) + ' x ' + parseInt(bbox.height, 10) );
                            }, 100)
                        };

                        if(self.shape.type === 'path'){
                            options.start = _.pick(this.attrs, ['cx', 'cy']); 
                            options.path = self.layer.attr('path');
                            options.pointIndex = this.pointIndex;
                        }

                        shapeResizer(self.layer, options);
                         

                        
                        if(this.type === 'circle'){
                            this.animate({
                                cx : stopPoint.x,
                                cy : stopPoint.y
                            });
                        } else {
                            this.animate(stopPoint);
                        }
                    }
                }
               
                /**
                 * Finish resize: sync the shape to the resized layer
                 * Used as handler dragend callback
                 * @private
                 */ 
                function resized(){
                    self.shape.animate(
                        _.pick(self.layer.attrs, ['x', 'y', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'path']),
                        200,
                        function animationEnd(){
                            self.setState('resizing', false)
                                .trigger('shapechange.qti-widget');
                        }
                    );
                    self.layer.remove();
                    self.layerTxt.remove();
                    
                    _.invoke(self.handlers, 'remove');
                    self.handlers = [];
                } 
               
                /**
                 * Resize a shape from keyboard
                 * @param {Number} code - the HTMLEvent's key code
                 * @private
                 */ 
                function keyResize(code){ 
                    var attr, stop, start;
                    if(!self.is('moving')){
                        self.setState('moving', true);
                        _.invoke(self.handlers, 'remove');
                        
                        attr = shape.attr();

                        switch(self.shape.type){

                            case 'rect' : 
                                start = { x: 0, y: 0 }; 
                                stop  = {
                                    x : (code === 39) ? attr.x + 1 : (code === 37) ? attr.x - 1 : attr.x,
                                    y : (code === 40) ? attr.y + 1 : (code === 38) ? attr.y - 1 : attr.y,
                                };
                                break;
                            case 'ellipse' : 
                            case 'circle' : 
                                start = { x: 0, y: 0 }; 
                                stop  = {
                                    x : (code === 39) ? attr.cx + 1 : (code === 37) ? attr.cx - 1 : attr.cx,
                                    y : (code === 40) ? attr.cy + 1 : (code === 38) ? attr.cy - 1 : attr.cy,
                                };
                                break;
                            case 'path' : 
                                start = { path : attr.path }; 
                                stop  = {
                                };
                                break;
                        }

                        shapeMover(
                            self.shape, start, stop
                        );
                        self.setState('moving', false)
                            .trigger('shapechange.qti-widget');
                    }
                }

                /**
                 * Start the moving the shape.
                 * Used as handler dragstart callback
                 * @private
                 */ 
                function startMove(x, y , event){
                    self.setState('moving', true)
                        .trigger('shapechanging.qti-widget');
                    
                    var mousePoint = graphicHelper.getPoint(event, paper, $container, isResponsive);
                    this.startPoint = {
                        x : mousePoint.x - this.attr('x'),
                        y : mousePoint.y - this.attr('y')
                    };
                    if(this.type === 'path'){
                        this.startPoint.path = this.attr('path');
                    }
                    self.shape.attr('cursor', 'move');
                    background.attr('cursor', 'move');
                    _.invoke(self.handlers, 'remove');                              
                }        
                
                /**
                 * Move a shape
                 * Used as handler drag callback
                 * @private
                 */ 
                function move(dx, dy, x, y, event){
                    var dest;
                    if(self.is('moving')){
                        shapeMover(
                            this, 
                            this.startPoint, 
                            graphicHelper.getPoint(event, paper, $container, isResponsive)
                        );
                    }
                }

                /**
                 * Finish moving
                 * Used as shape dragend callback
                 * @private
                 */ 
                function moved(){
                    delete this.startPoint;
                    self.shape.attr('cursor', 'pointer');
                    background.attr('cursor', 'default');

                    self.setState('moving', false)
                        .trigger('shapechange.qti-widget');
                }

                return this;
            },
            
            /**
             * Stop the shape handling 
             * @returns {shapeEditor.editor} the shape editor instance
             */ 
            quitHandling : function quitHandling(){
                $(document).off('keydown.qti-widget');

                this.shape.undrag();
                _.invoke(this.handlers, 'remove');
                this.handlers = [];

                this.setState('moving', false)
                    .setState('resizing', false)
                    .setState('handling', false)
                    .trigger('quithandling.qti-widget'); 
                
                return this;
            },

            /**
             * Removes the editor's shape 
             * @returns {shapeEditor.editor} the shape editor instance
             */ 
            removeShape : function removeShape(){
                var id, data;
                if(this.shape){
                    this.quitHandling();
                    id = this.shape.id;
                    data = this.shape.data;
                    this.shape.remove();
                    this.trigger('remove.qti-widget', id, data); 
                }
                
                return this;
            },

            /**
             * Clean up editor in case of reference keeping 
             * @returns {shapeEditor.editor} the shape editor instance
             */ 
            destroy : function destroy(){
                if(this.is('handling')){
                    this.quitHandling();
                }
                if(this.shape){
                    this.shape.unclick();
                    background.unclick();
                }
                this.shape = null;
                this.handlers  = [];
                this._events  = {};
                return this;
            }
        };
        
        return editor._init(shape);
    };

    return shapeEditor;
});
