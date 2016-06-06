/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'raphael',
    'scale.raphael',
    'json!taoQtiItem/qtiCommonRenderer/renderers/graphic-style.json'
], function($, _, raphael, scaleRaphael, gstyle){
    'use strict';

    //maps the QTI shapes to Raphael shapes
    var shapeMap = {
        'default' : 'rect',
        'poly'    : 'path'
    };

    //length constraints to validate coords
    var coordsValidator = {
        'rect' : 4,
        'ellipse' : 4,
        'circle' : 3,
        'poly' : 6,
        'default' : 0
    };

    //transform the coords from the QTI system to Raphael system
    var qti2raphCoordsMapper = {

        /**
         * Rectangle coordinate mapper:  from left-x,top-y,right-x-bottom-y to x,y,w,h
         * @param {Array} coords - QTI coords
         * @returns {Array} raphael coords
         */
        'rect' : function(coords){
           return [
                coords[0],
                coords[1],
                coords[2] - coords[0],
                coords[3] - coords[1]
            ];
        },

        /**
         * Creates the coords for a default shape (a rectangle that covers all the paper)
         * @param {Raphael.Paper} paper - the paper
         * @returns {Array} raphael coords
         */
        'default' : function(paper){
           return [ 0, 0, paper.width, paper.height ];
        },

        /**
         * polygone coordinate mapper:  from x1,y1,...,xn,yn to SVG path format
         * @param {Array} coords - QTI coords
         * @returns {Array} path desc
         */
        'poly' : function(coords){
            var a;
            var size = coords.length;

            // autoClose if needed
            if((coords[0] !== coords[size - 2]) && (coords[1] !== coords[size - 1])){
                coords.push(coords[0]);
                coords.push(coords[1]);
            }

            // move to first point
            coords[0] = "M" + coords[0];
            for(a = 1; a < size; a++){
                if(a % 2 === 0){
                    coords[a] = "L" + coords[a];
                }
            }
            return [coords.join(" ")];
        }
    };

    //transform the coords from a raphael shape to the QTI system
    var raph2qtiCoordsMapper = {

        /**
         * Rectangle coordinate mapper: from x,y,w,h to left-x,top-y,right-x-bottom-y
         * @param {Object} attr - Raphael Element's attributes
         * @returns {Array} qti based coords
         */
        'rect' : function(attr){
           return [
                attr.x,
                attr.y,
                attr.x + attr.width,
                attr.y + attr.height
            ];
        },

        /**
         * Circle coordinate mapper
         * @param {Object} attr - Raphael Element's attributes
         * @returns {Array} qti based coords
         */
        'circle' : function(attr){
           return [
                attr.cx,
                attr.cy,
                attr.r
           ];
        },

        /**
         * Ellispe coordinate mapper
         * @param {Object} attr - Raphael Element's attributes
         * @returns {Array} qti based coords
         */
        'ellipse' : function(attr){
           return [
                attr.cx,
                attr.cy,
                attr.rx,
                attr.ry
            ];
        },

        /**
         * Get the coords for a default shape (a rectangle that covers all the paper)
         * @param {Object} attr - Raphael Element's attributes
         * @returns {Array} qti based coords
         */
        'default' : function(attr){
           return this.rect(attr);
        },

        /**
         * polygone coordinate mapper:  from SVG path (available as segments) to x1,y1,...,xn,yn format
         * @param {Raphael.Paper} paper - the paper
         * @returns {Array} raphael coords
         */
        'path' : function(attr){
            var poly = [];
            var i;
            if(_.isArray(attr.path)){
               for(i = 1; i < attr.path.length; i++){
                    if(attr.path[i].length === 3){
                        poly.push(attr.path[i][1]);
                        poly.push(attr.path[i][2]);
                    }
               }
            }
            return poly;
        }
    };



    /**
     * Graphic interaction helper
     * @exports qtiCommonRenderer/helpers/Graphic
     */
    var GraphicHelper = {

        /**
         * Raw access to the styles
         * @type {Object}
         */
        _style : gstyle,


        /**
         * Apply the style defined by name to the element
         * @param {Raphael.Element} element - the element to change the state
         * @param {String} state - the name of the state (from states) to switch to
         */
        setStyle : function(element, name){
            if(element && gstyle[name]){
                element.attr(gstyle[name]);
            }
        },


        /**
         * Create a Raphael paper with a bg image, that is width responsive
         * @param {String} id - the id of the DOM element that will contain the paper
         * @param {String} serial - the interaction unique indentifier
         * @param {Object} options - the paper parameters
         * @param {String} options.img - the url of the background image
         * @param {jQueryElement} [options.container] - the parent of the paper element (got the closest parent by default)
         * @param {Number} [options.width] - the paper width
         * @param {Number} [options.height] - the paper height
         * @param {String} [options.imgId] - an identifier for the image element
         * @param {Function} [options.done] - executed once the image is loaded
         * @returns {Raphael.Paper} the paper
         */
        responsivePaper : function(id, serial, options){

            var paper, image;

            var $container = options.container || $('#' + id).parent();
            var $editor    = $('.image-editor', $container);
            var $body  = $container.closest('.qti-itemBody');
            var factory = raphael.type === 'SVG' ? scaleRaphael : raphael;

            var width = options.width || $container.innerWidth();
            var height = options.height || $container.innerHeight();

            var resizer = _.throttle(resizePaper, 10);

            //padding and border diff. always add 1px to cover the rounded value in scalling


            paper = factory.call(null ,id, width, height);
            image = paper.image(options.img, 0, 0, width, height);
            if(options.imgId){
                image.id = options.imgId;
            }

            //retry to resize once the SVG is loaded
            $(image.node)
              .attr('externalResourcesRequired','true')
              .on("load", function() {
                resizePaper();
              });

            if(raphael.type === 'SVG'){

                //scale on creation
                resizePaper();

                $(window).on('resize.qti-widget.'  + serial, resizer);
                $(document).on('customcssloaded.styleeditor', function(){
                    _.delay(resizer, 200);
                });
                $container.on('resize.qti-widget.' + serial , function(e, givenWidth){
                    resizer(e, givenWidth);
                });

            } else {
                paper.canvas.setAttribute('viewBox', '0 0 ' + width + ' ' + height);
                $container.find('.main-image-box').width(width);
                if(typeof options.resize === 'function'){
                    options.resize(width, 1);
                }
            }

            /**
             * scale the raphael paper
             * @private
             */
            function resizePaper(e, givenWidth){
                if(e){
                    e.stopPropagation();
                }

                var factor          = 1;
                var diff            = ($editor.outerWidth() - $editor.width()) + ($container.outerWidth() - $container.width()) + 1;
                var maxWidth        = $body.width();
                var containerWidth  = $container.innerWidth();

                if(containerWidth > 0 || givenWidth > 0){


                    if(givenWidth < containerWidth && givenWidth < maxWidth){
                        containerWidth = givenWidth - diff;
                    } else if(containerWidth > maxWidth){
                        containerWidth = maxWidth - diff;
                    } else {
                        containerWidth -= diff;
                    }

                    if($container.hasClass('responsive')){
                        factor = containerWidth / width;

                        paper.changeSize(containerWidth, height * factor, false, false);
                        paper.scaleAll( factor );
                    } else {
                        paper.changeSize(containerWidth, height, false, false);
                    }
                    if(typeof options.resize === 'function'){
                        options.resize(containerWidth, factor);
                    }
                    $container.trigger('resized.qti-widget');
                }
            }

            return paper;
        },

        /**
         * Create a new Element into a raphael paper
         * @param {Raphael.Paper} paper - the interaction paper
         * @param {String} type - the shape type
         * @param {String|Array.<Number>} coords - qti coords as a string or an array of number
         * @param {Object} [options] - additional creation options
         * @param {String} [options.id] - to set the new element id
         * @param {String} [options.title] - to set the new element title
         * @param {String} [options.style = basic] - to default style
         * @param {Boolean} [options.hover = true] - to disable the default hover state
         * @param {Boolean} [options.touchEffect = true] - a circle appears on touch
         * @param {Boolean} [options.qtiCoords = true] - if the coords are in QTI format
         * @returns {Raphael.Element} the created element
         */
        createElement : function(paper, type, coords, options){
            var self = this;
            var element;
            var bbox;
            var shaper = shapeMap[type] ? paper[shapeMap[type]] : paper[type];
            var shapeCoords = options.qtiCoords !== false ? self.raphaelCoords(paper, type, coords) : coords;

            if(typeof shaper === 'function'){
               element = shaper.apply(paper, shapeCoords);
               if(element){
                    if(options.id){
                        element.id = options.id;
                    }
                    if(options.title){
                        element.attr('title', options.title);
                    }
                    element.attr(gstyle[options.style || 'basic'])
                            .toFront();

                     //preven issue in firefox 37
                    $(element.node).removeAttr('stroke-dasharray');

                    if(options.hover !== false){
                      element.hover(function(){
                            if(!element.flashing){
                                self.updateElementState(this, 'hover');
                            }
                      }, function(){
                            if(!element.flashing){
                                self.updateElementState(this, this.active ? 'active' : this.selectable ? 'selectable' : 'basic');
                            }
                      });
                    }

                   if(options.touchEffect !== false){
                       element.touchstart(function(){
                            self.createTouchCircle(paper, element.getBBox());
                       });
                   }
               }

            } else {
                throw new Error('Unable to find method ' + type + ' on paper');
            }
            return element;
        },

        /**
         * Create target point
         * @param {Raphael.Paper} paper - the paper
         * @param {Object} [options]
         * @param {Object} [options.id] - and id to identify the target
         * @param {Object} [options.point] - the point to add to the paper
         * @param {Number} [options.point.x = 0] - point's x coord
         * @param {Number} [options.point.y = 0] - point's y coord
         * @param {Boolean} [options.hover] = true - the target has an hover effect
         * @param {Function} [options.create] - call once created
         * @param {Function} [options.remove] - call once removed
         */
        createTarget : function createTarget(paper, options){
            var self    = this;
            options     = options || {};
            var point   = options.point || {x : 0, y : 0};
            var baseSize= 18;
            var factor  = (paper.w && paper.width) ? paper.width / paper.w : 1;
            var size    = factor !== 1 ? Math.floor(18 / factor) + 1 : baseSize;
            var half    = size / 2;
            var x       = point.x >= half ? point.x - half : 0;
            var y       = point.y >= half ? point.y - half : 0;
            var hover   = typeof options.hover === 'undefined' ? true : !!options.hover;
            var tBBox;

            //create the target from a path
            var target = paper
                .path(gstyle.target.path)
                .transform('T' + x + ',' + y + 's' + size / baseSize)
                .attr(gstyle.target)
                .attr('title', _('Click again to remove'));

            //generate an id if not set in options
            if(options.id){
                target.id = options.id;
            } else {
                var count = 0;
                paper.forEach(function(element){
                    if(element.data('target')){
                        count++;
                    }
                });
                target.id = 'target-' + count;
            }

            tBBox = target.getBBox();

            //create an invisible rect over the target to ensure path selection
            var layer = paper
                .rect(tBBox.x, tBBox.y, tBBox.width, tBBox.height)
                .attr(gstyle.layer)
                .click(function(){
                    var id = target.id;
                    var point = this.data('point');
                    if(_.isFunction(options.select)){
                        options.select(target, point, this);
                    }
                    if(_.isFunction(options.remove)){
                        this.remove();
                        target.remove();
                        options.remove(id, point);
                    }
                });
            if(hover){
                layer.hover(function(){
                    if(!target.flashing){
                        self.setStyle(target, 'target-hover');
                    }
                }, function(){
                    if(!target.flashing){
                        self.setStyle(target, 'target-success');
                    }
                });
            }

            layer.id = 'layer-' + target.id;
            layer.data('point', point);
            target.data('target', point);

            if(_.isFunction(options.create)){
                options.create(target);
            }

            return target;
        },


        /**
         * Get the Raphael coordinate from QTI coordinate
         * @param {Raphael.Paper} paper - the interaction paper
         * @param {String} type - the shape type
         * @param {String|Array.<Number>} coords - qti coords as a string or an array of number
         * @returns {Array} the arguments array of coordinate to give to the approriate raphael shapre creator
         */
        raphaelCoords :  function raphaelCoords(paper, type, coords){
            var shapeCoords;
            if(_.isString(coords)){
                coords = _.map(coords.split(','), function(coord){
                    return parseInt(coord, 10);
                });
            }
            if(!_.isArray(coords) || coords.length < coordsValidator[type]){
                throw new Error('Invalid coords ' + JSON.stringify(coords) + '  for type ' + type);
            }
            switch(type){
                case 'rect' : shapeCoords = qti2raphCoordsMapper.rect(coords); break;
                case 'default' : shapeCoords = qti2raphCoordsMapper['default'].call(null, paper); break;
                case 'poly' : shapeCoords = qti2raphCoordsMapper.poly(coords); break;
                default : shapeCoords = coords; break;
            }
            return shapeCoords;
        },

        /**
         * Get the QTI coordinates from a Raphael Element
         * @param {Raphael.Element} element - the shape to get the coords from
         * @returns {String} the QTI coords
         */
        qtiCoords : function qtiCoords(element){
            var mapper = raph2qtiCoordsMapper[element.type];
            var result = '';
            if(_.isFunction(mapper)){
                result = _.map(mapper.call(raph2qtiCoordsMapper, element.attr()), function(coord){
                            return _.parseInt(coord);
                        }).join(',');
            }
            return result;
        },

        /**
         * Create a circle that animate and disapear from a shape.
         *
         * @param {Raphael.Paper} paper - the paper
         * @param {Raphael.Element} element - used to get the bbox from
         */
        createTouchCircle : function(paper, bbox){
            var radius = bbox.width > bbox.height ? bbox.width : bbox.height;
            var tCircle = paper.circle( (bbox.x + (bbox.width / 2)),  (bbox.y + (bbox.height / 2)), radius);
            tCircle.attr(gstyle['touch-circle']);
            _.defer(function(){
                tCircle.animate({'r' : radius + 5, opacity: 0.7}, 300, function(){
                    tCircle.remove();
                });
            });
        },

        /**
         * Create a text, that scales.
         *
         * @param {Raphael.Paper} paper - the paper
         * @param {Object} options - the text options
         * @param {Number} options.left - x coord
         * @param {Number} options.top - y coord
         * @param {String} [options.content] - the text content
         * @param {String} [options.id] - the element identifier
         * @param {String} [options.style = 'small-text'] - the style name according to the graphic-style.json keys
         * @param {String} [options.title] - the text tooltip content
         * @param {Boolean} [options.hide = false] - if the text starts hidden
         * @returns {Raphael.Element} the created text
         */
        createText : function(paper, options){
            var fontSize, scaledFontSize;
            var top     = options.top || 0;
            var left    = options.left || 0;
            var content = options.content || '';
            var style   = options.style || 'small-text';
            var title   = options.title || '';
            var factor  = 1;

            if(paper.width && paper.w){
                factor = paper.width / paper.w;
            }

            var text = paper.text(left , top, content).toFront();
            if(options.id){
                text.id = options.id;
            }
            if(options.hide){
                text.hide();
            }

            text.attr(gstyle[style]);

            if(typeof factor !== 'undefined' && factor !== 1){
                fontSize = parseInt(text.attr('font-size'), 10);
                scaledFontSize   = Math.floor(fontSize / factor) + 1;

                text.attr('font-size', scaledFontSize);
            }

            if(title){
                this.updateTitle(text, title);
            }
            return text;
        },

        /**
         * Create a text in the middle of the related shape.
         *
         * @param {Raphael.Paper} paper - the paper
         * @param {Raphael.Element} shape - the shape to add the text to
         * @param {Object} options - the text options
         * @param {String} [options.content] - the text content
         * @param {String} [options.id] - the element identifier
         * @param {String} [options.style = 'small-text'] - the style name according to the graphic-style.json keys
         * @param {String} [options.title] - the text tooltip content
         * @param {Boolean} [options.hide = false] - if the text starts hidden
         * @param {Boolean} [options.shapeClick = false] - clicking the text delegates to the shape
         * @returns {Raphael.Element} the created text
         */
        createShapeText : function(paper, shape, options){
            var self    = this;
            var fontSize, scaledFontSize;
            var bbox    = shape.getBBox();

            var text = this.createText(paper, _.merge({
                left : bbox.x + (bbox.width / 2),
                top  : bbox.y + (bbox.height / 2)
            }, options));

            if(options.shapeClick){
                text.click(function(){
                    self.trigger(shape, 'click');
                });
            }
            return text;
        },

        /**
         * Create an image with a padding and a border, using a set.
         *
         * @param {Raphael.Paper} paper - the paper
         * @param {Object} options - image options
         * @param {Number} options.left - x coord
         * @param {Number} options.top - y coord
         * @param {Number} options.width - image width
         * @param {Number} options.height - image height
         * @param {Number} options.url - image ulr
         * @param {Number} [options.padding = 6] - a multiple of 2 is welcomed
         * @param {Boolean} [options.border = false] - add a border around the image
         * @param {Boolean} [options.shadow = false] - add a shadow back the image
         * @returns {Raphael.Element} the created set, augmented of a move(x,y) method
         */
        createBorderedImage : function(paper, options){
            var padding = options.padding >= 0 ? options.padding : 6;
            var halfPad = padding / 2;

            var rx = options.left,
                ry = options.top,
                rw = options.width + padding,
                rh = options.height + padding;

            var ix = options.left + halfPad,
                iy = options.top + halfPad,
                iw = options.width,
                ih = options.height;

            var set = paper.set();

            //create a rectangle with a padding and a border.
            var rect = paper
                .rect(rx, ry, rw, rh)
                .attr(options.border ? gstyle['imageset-rect-stroke'] : gstyle['imageset-rect-no-stroke']);

            //and an image centered into the rectangle.
            var image = paper
                .image(options.url, ix, iy, iw, ih)
                .attr(gstyle['imageset-img']);

            if(options.shadow){
                set.push(rect.glow({
                    width   : 2,
                    offsetx : 1,
                    offsety : 1
                }));
            }
            set.push(rect, image);

            /**
             * Add a move method to set that keep the given coords during an animation
             * @private
             * @param {Number} x - destination
             * @param {Number} y - destination
             * @param {Number} [duration = 400] - the animation duration
             * @returns {Raphael.Element} the set for chaining
             */
            set.move = function move(x, y, duration){
                var animation = raphael.animation({x: x, y : y}, duration || 400);
                var elt = rect.animate(animation);
                image.animateWith(elt, animation, {x : x + halfPad, y : y + halfPad}, duration || 400);
                return set;
            };

            return set;
        },

        /**
         * Update the visual state of an Element
         * @param {Raphael.Element} element - the element to change the state
         * @param {String} state - the name of the state (from states) to switch to
         * @param {String} [title] - a title linked to this step
         */
        updateElementState : function(element, state, title){
            if(element && element.animate){
                element.animate(gstyle[state], 200, 'linear', function(){
                    element.attr(gstyle[state]); //for attr that don't animate

                     //preven issue in firefox 37
                     $(element.node).removeAttr('stroke-dasharray');
                });

                if(title){
                    this.updateTitle(element, title);
                }
            }
        },

        /**
         * Update the title of an element (the attr method of Raphael adds only new node instead of updating exisitings).
         * @param {Raphael.Element} element - the element to update the title
         * @param {String} [title] - the new title
         */
        updateTitle : function(element, title){
            if(element && element.node){
                //removes all remaining titles nodes
                _.forEach(element.node.children, function(child){
                    if(child.nodeName.toLowerCase() === 'title'){
                        element.node.removeChild(child);
                    }
                });

                //then set the new title
                element.attr('title', title);
            }
        },

        /**
         * Highlight an element with the error style
         * @param {Raphael.Element} element - the element to hightlight
         * @param {String} [restorState = 'basic'] - the state to restore the elt into after flash
         */
        highlightError : function(element, restoredState){
            var self = this;
            if(element){
               element.flashing = true;
               self.updateElementState(element, 'error');
                _.delay(function(){
                    self.updateElementState(element, restoredState || 'basic');
                    element.flashing = false;
                }, 800);
           }
        },

        /**
         * Trigger an event already bound to a raphael element
         * @param {Raphael.Element} element
         * @param {String} event - the event name
         *
         */
        trigger : function(element, event){

            var evt = _.where(element.events, { name : event});
            if(evt.length && evt[0] && typeof evt[0].f === 'function'){
                evt[0].f.apply(element, Array.prototype.slice.call(arguments, 2));
            }
        },


        /**
         * Get an x/y point from a MouseEvent
         * @param {MouseEvent} event - the source event
         * @param {Raphael.Paper} paper - the interaction paper
         * @param {jQueryElement} $container - the paper container
         * @param {Boolean} isResponsive - if the paper is scaling
         * @returns {Object} x,y point
         */
        getPoint : function getPoint(event, paper, $container, isResponsive){
            var rwidth, rheight, wfactor;

            //get the click coords
            var point = this.clickPoint($container, event);

            //recalculate point coords in case of scaled image.
            if(paper.w && paper.w !== paper.width){
                if(isResponsive){
                    wfactor = paper.w / paper.width;
                    point.x = Math.round(point.x * wfactor);
                    point.y = Math.round(point.y * wfactor);
                } else if(paper.width > paper.w){
                    rwidth = (paper.width - paper.w) / 2;
                    point.x = Math.round(point.x - rwidth);
                } else {
                    wfactor = paper.w / paper.width;
                    point.x = Math.round(point.x * wfactor);

                    rheight = (paper.height - (paper.height * (2 - wfactor))) / 2;
                    point.y = Math.round((point.y * wfactor) - rheight);
                }
            }

            return point;
        },

        /**
         * Get paper position relative to the container
         * @param {jQueryElement} $container - the paper container
         * @param {Raphael.Paper} paper - the interaction paper
         * @returns {Object} position with top and left
         */
        position : function($container, paper){
            var pw = parseInt(paper.w || paper.width, 10);
            var cw = parseInt($container.width(), 10);
            var ph = parseInt(paper.w || paper.width, 10);
            var ch = parseInt($container.height(), 10);

            return {
                left : ((cw - pw) / 2),
                top : ((ch - ph) / 2)
            };
        },

        /**
         * Get a point from a click event
         * @param {jQueryElement} $container - the element that contains the paper
         * @param {MouseEvent} event - the event triggered by the click
         * @returns {Object} the x,y point
         */
        clickPoint : function($container, event){
            var x, y;
            var offset = $container.offset();
             if (event.pageX || event.pageY) {
                x = event.pageX - offset.left;
                y = event.pageY - offset.top;
            } else if (event.clientX || event.clientY) {
                x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft - offset.left;
                y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop - offset.top;
            }

            return { x : x, y : y };
        }
    };

    return GraphicHelper;
});
