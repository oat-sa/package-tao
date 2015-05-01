/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery', 'lodash', 
    'taoQtiItem/qtiCommonRenderer/helpers/Graphic'
], function($, _, commonGraphicHelper){


    /**
     * Resize a Raphael shape.
     *
     * @exports taoQtiItem/qtiCreator/widgets/interactions/helpers/shapeResizer
     * @param {Raphael.Element} element - the element to resize
     * @param {Object} options - the sizing options
     * @param {Object} [options.start] - the x,y starting point of the shape (prior to the resize)
     * @param {Object} options.stop - the x,y stop point of the shape (the size is the difference between start and stop)
     * @param {Object} [options.constraints] - define sizing constraints, ie. {x : true} for horizontal only sizing
     * @param {Number} [options.pointIndex] - for a path, this is the index of the point into the path
     * @param {Function} [options.resized] - called back on resized
     */
    var resize =  function resize (element, options){
        var resizer;
        
        if(element && element.type && options.stop){
            resizer = _.bind(shapeResizer[element.type], shapeResizer);
            
            if(!options.start){
                options.start = getStartPoint(element);
            }
            
            if(_.isFunction(resizer)){
                element.attr(resizer(options));
                if (_.isFunction(options.resized)) {
                    options.resized.call(element);
                }
            }
        }

        /**
         * Get the start point of a shape
         * @returns {Object} the start point
         */
        function getStartPoint(){
            var startPoint;
            switch(element.type){
                case 'circle'   : startPoint = { x : element.attrs.cx, y : element.attrs.cy }; break;
                case 'ellipse'  : 
                case 'rect'     : startPoint = element.getBBox(); break;
            }
            return startPoint;
        }        
    };
    
    /**
     * Provides resizing implementation based on the shape type
     */ 
    var shapeResizer = {
        
        /**
         * Resize a rectangle
         * @param {Object} options - see resize
         * @returns {Object} attributes with the new size the Raphael.Element accepts
         */
        rect : function resizeRectangle(options){
            var start = options.start;
            var stop = options.stop;
            var constraints = options.constraints;
            var dest = {};
            
            if(!constraints || constraints.x){
                dest.width = stop.x - start.x;
                if(dest.width < 0){
                    dest.x = stop.x;
                    dest.width = dest.width * -1;
                } 
            }

            if(!constraints || constraints.y){
                dest.height = stop.y - start.y;
                if(dest.height < 0){
                    dest.y = stop.y;
                    dest.height = dest.height * -1;
                }
            }
            if(constraints && constraints.ix){
                dest.x = stop.x;
                dest.width = start.width - (stop.x - start.x);
                if(dest.width < 0){
                    dest.x = stop.x;
                    dest.width = dest.width * -1;
                } 
            }

            if(constraints && constraints.iy){
                dest.y = stop.y;
                dest.height = start.height - (stop.y - start.y);
                if(dest.height < 0){
                    dest.y = stop.y;
                    dest.height = dest.height * -1;
                }
            }
            
            //we keep a minimal size
            if(dest.width < 10){
                dest.width = 10;
            }
            if(dest.height < 10){
                dest.height = 10;
            }
            return dest;
        },
        
        /**
         * Resize a circle
         * @param {Object} options - see resize
         * @returns {Object} attributes with the new size the Raphael.Element accepts
         */
        circle : function resizeCircle(options){
            var start = options.start;
            var stop = options.stop;
            var rw = (stop.x > start.x) ? stop.x - start.x  : start.x - stop.x;
            var rh = (stop.y > start.y) ? stop.y - start.y  : start.y - stop.y;
            //thanks pythagore 
            var r =  Math.floor( Math.sqrt(Math.pow(rw, 2) + Math.pow(rh, 2)));

            return {
                r : r < 5 ? 5 : r
            };
        },

        /**
         * Resize an ellispe
         * @param {Object} options - see resize
         * @returns {Object} attributes with the new size the Raphael.Element accepts
         */
        ellipse : function resizeEllipse(options){
            var dest = {};
            var boxDest = this.rect(options); 

            if(boxDest.x){
                dest.x = boxDest.x;
            }

            if(boxDest.y){
                dest.y = boxDest.y;
            }
            
            if(boxDest.width){
                dest.rx = boxDest.width / 2;
            }
            
            if(boxDest.height){
                dest.ry = boxDest.height / 2;
            }
            return dest;
        },

        /**
         * Resize a path
         * @param {Object} options - see resize (pointIndex is required here)
         * @returns {Object} attributes with the new size the Raphael.Element accepts
         */
        path : function resizePath(options){
            var dest = {};
            if(options.path && options.start){
                dest.path = '';
               _.forEach(options.path, function(point, index){
                   dest.path += point[0];
                   if(point.length === 3){
                       if (index === options.pointIndex || options.pointIndex === 0 && index === 1){
                            dest.path += options.stop.x + ' ' + options.stop.y;
                       } else {
                            dest.path += point[1] + ' ' + point[2];
                       }
                   }                 
               }); 
            }
            return dest;
        }
    };
    
    return resize;
});
