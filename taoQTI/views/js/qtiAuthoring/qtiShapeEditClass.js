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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

function qtiShapesEditClass(canvasContainerIdentifier, backgroundImagePath, options){
	if(!Raphael){
		throw 'The raphael graphic library is required.';
	}
	
	var defaultOptions = {
		stroke: '#ABA9AA',
		fill: '#E0DCDD',
		opacity: .5,
		strokeHover: 'black',
		fillHover: '#E6E6E6',
		opacityHover: .8,
		backgroundImage: '',
		width: 0,
		height: 0,
		pointSize: 4,
		onDrawn: function(id, shapeObject, self){},
		onReady: function(self){}
	}
	
	this.options = $.extend(defaultOptions, options);
	
	this.canvasContainerIdentifier = canvasContainerIdentifier;
	var $canvasContainer = $('#'+this.canvasContainerIdentifier);
	if(!$canvasContainer.length){
		throw 'The canvas dom element not found.';
	}
	$canvasContainer.empty();//clear it first
	
	// var canvasIdentifier = 'canvas_'+canvasContainerIdentifier;
	this.canvasContainerIdentifier = this.canvasContainerIdentifier;
	this.canvasIdentifier = 'canvas_'+this.canvasContainerIdentifier;
	this.canvasBackgroundIdentifier = "backgroungImage_"+this.canvasContainerIdentifier;
	
	var $canvas = $('<div id="'+this.canvasIdentifier+'"/>').appendTo($canvasContainer);
	$canvas.css('position', 'absolute');
	$canvas.css('top', 0);
	$canvas.css('left', 0);
	
	var $imgElt = $('<img id="'+this.canvasBackgroundIdentifier+'" src="'+util.getMediaResource(backgroundImagePath)+'"/>').appendTo($canvasContainer);
	
	this.shape = 'point';//current shape:
	this.currentId = 0; //the current working id
	
	this._shapeObj = null;
	this.path = [];//current path
	
	this.startPoint = null;
	this.endPoint = null;
	this.shapes = [];
	
	var self = this;
		
	$imgElt.load(function(){
		
		var width = 0;
		var height = 0;
		if(!self.options.width){
			width = $imgElt.width();
			$('#InteractionForm').find('#object_width').val(width);
		}else{
			width = self.options.width;
		}
		if(!self.options.height){
			height = $imgElt.height();
			$('#InteractionForm').find('#object_height').val(height);
		}else{
			height = self.options.height;
		}
		
		$imgElt.attr('width', width);
		$imgElt.attr('height', height);
		
		var $canvasContainer = $('#'+self.canvasContainerIdentifier);
		$canvasContainer.width(width);
		$canvasContainer.height(height);
		
		$canvas.width(width);
		$canvas.height(height);
		
		self.canvas = Raphael(self.canvasIdentifier, width, height);
		self.initEventHandlers();
		
		if(self.options.onReady) self.options.onReady(self);
		
		$(this).unbind('load');
	});
	
}


qtiShapesEditClass.round = function(number, digit){

	var newNumber = number;
	if(!digit){
		digit = 0;
	}
	
	if(digit>0){
		newNumber = Math.round(number*Math.pow(10,digit))/Math.pow(10,digit);
	}else if(digit == 0){
        newNumber = Math.round(number);
    }
	
	return newNumber;
}
_round = qtiShapesEditClass.round;

qtiShapesEditClass.prototype.setWidth = function(width){
	var $canvasContainer = $('#'+this.canvasContainerIdentifier);
	var height = $canvasContainer.height();
	this.setSize(width, height);
}

qtiShapesEditClass.prototype.setHeight = function(height){
	var $canvasContainer = $('#'+this.canvasContainerIdentifier);
	var width = $canvasContainer.width();
	this.setSize(width, height);
}

qtiShapesEditClass.prototype.setSize = function(width, height){
	
	var $canvas = $('#'+this.canvasIdentifier);
	if(!$canvas.length){
		throw 'The canvas dom element not found.';
	}
	$canvas.width(width);
	$canvas.height(height);
	
	var $canvasContainer = $('#'+this.canvasContainerIdentifier);
	$canvasContainer.width(width);
	$canvasContainer.height(height);
	
	var $imgElt = $('#'+this.canvasBackgroundIdentifier);
	$imgElt.width(width);
	$imgElt.height(height);
	
	this.canvas.setSize(width, height);
}

qtiShapesEditClass.prototype.setBackground = function(backgroundImagePath, width, height){
	
	var $canvas = $('#'+this.canvasIdentifier);
	if(!$canvas.length){
		throw 'The canvas dom element not found.';
	}
	
	var $imgElt = $('#'+this.canvasBackgroundIdentifier);
	$imgElt.attr('src', util.getMediaResource(backgroundImagePath));
	
	var self = this;
	
	$imgElt.load(function(){
		
		if(!width){
			width = $imgElt.width();
			$('#InteractionForm').find('#object_width').val(width);
		}
		if(!height){
			height = $imgElt.height();
			$('#InteractionForm').find('#object_height').val(height);
		}
		
		$imgElt.attr('width', width);
		$imgElt.attr('height', height);
		
		var $canvasContainer = $('#'+self.canvasContainerIdentifier);
		$canvasContainer.width(width);
		$canvasContainer.height(height);
		
		$canvas.width(width);
		$canvas.height(height);
		
		self.canvas.setSize(width, height);
		
		$(this).unbind('load');
	});	
}

qtiShapesEditClass.prototype.setCurrentShape = function(shape){
	this.shape = shape;
}

qtiShapesEditClass.prototype.setCurrentId = function(id){
	if(id){
		this.currentId = id;
	}
}

qtiShapesEditClass.mousePosition = function(event, relativeTo){
	
	var parentOffset = $(relativeTo).offset();
	var position = {
		x : _round(event.pageX - parentOffset.left),
		y : _round(event.pageY - parentOffset.top)
	};
	
	// CL('postion', position);
	// CD({
		// 'pageX':event.pageX, 
		// 'pageY':event.pageY, 
		// 'offsetLeft':parentOffset.left, 
		// 'offsetTop':parentOffset.top,
		// 'scrollLeft':$(window).scrollLeft(),
		// 'scrollTop':$(window).scrollTop()
	// }, 'details');
	
	return position;
}


qtiShapesEditClass.prototype.initEventHandlers = function(){
	var self = this;
	var $canvas = $('#'+this.canvasContainerIdentifier);
	
	if($canvas.length){
				
		//mouse down event: init the shape:
		$canvas.mousedown(function(e){

			e.preventDefault();
			
			if(self.shape && self.currentId){
			
				var cursorPosition = qtiShapesEditClass.mousePosition(e, this);
				
				if(self.shape == 'path'){
					if(!self.focused){
						self.focused = true;
						self.removeShapeObj(self.currentId);
						self.path = [];//reinit the path array
					}
				}else{
					self.focused = true;
					self.startPoint = cursorPosition;
					self.removeShapeObj(self.currentId);
				}
				
			}
		});
		
		//mouse move event: real time update the current shape (but "path")
		$canvas.mousemove(function(e){
			e.preventDefault();
			
			if(self.focused && self.startPoint){
				if(self.shape){
					
					if(self.shape == 'path') return false; //comment this line to allow drawing with pencil!
					if(self.shape == 'point') return false;
					
					var position = qtiShapesEditClass.mousePosition(e, this);
					
					if(self.canvas[self.shape]){
						self.setShapeObj(self.drawShape(self.startPoint, position), self.currentId);
					}
					
				}
			}
		});
		
		//mouse up event: finishing the shap drawing (but "path")
		$canvas.mouseup(function(e){
			e.preventDefault();
			
			if(self.shape != 'path' && self.shape != 'point'){
				
				self.stopDrawing(self.options.onDrawn);
			}		
		});
		
		//mouse click event: append a point to the "path" ("path" shape only)
		$canvas.click(function(e){
			
			e.preventDefault();
			
			if(self.focused && self.shape == 'path'){
				var position = qtiShapesEditClass.mousePosition(e, this);
				if(self.canvas[self.shape]){
					if(self.path.length>0){
						self.path.pop();//remove the latest point (which is the original point)
					}
					self.setShapeObj(self.drawShape(null, position), self.currentId);//set in the temp shape object
					self.setShapeObj(self.drawShape(null, self.path[0]), self.currentId);//draw the real shape
				}
				
			}else if(self.shape == 'point' && self.currentId){
				//allow point drawing all the time
				var position = qtiShapesEditClass.mousePosition(e, this);
				self.setShapeObj(self.drawShape(position, null, 'point'), self.currentId);
				self.stopDrawing(self.options.onDrawn);
			}
		});
			   
		$('#'+self.canvasContainerIdentifier).hover(function(){
			if(self.currentId) self.highlightShape(self.currentId, 500);
		}, function(){
			self.stopDrawing(self.options.onDrawn);
		});
	}
	
}

qtiShapesEditClass.prototype.startDrawing = function(id, shape){
	if(id && shape){
		if(shape == 'poly'){
			shape = 'path';
		}
		this.currentId = id;
		this.shape = shape;
		this.drawing = true;
		this.focused = false;
		
		$('#'+this.canvasIdentifier).addClass('qtiShape-drawing');
	}
}

qtiShapesEditClass.prototype.highlightShape = function(id, speed){
	//highlight for a while:
	if(speed) speed = 1000;
	if(this.shapes[id]){
		var raphaelObject = this.shapes[id].raphaelObject;
		if(raphaelObject){
			raphaelObject.toFront(); 
			raphaelObject.attr('fill', this.options.fillHover);
			raphaelObject.attr('stroke', this.options.strokeHover);
			raphaelObject.attr('fill-opacity', this.options.opacityHover);
			raphaelObject.animate({'fill':this.options.fill, 'stroke':this.options.stroke, 'fill-opacity':this.options.opacity}, speed);
		}
	}
}

/**
 * Called when a shape has just been drawn
 */
qtiShapesEditClass.prototype.stopDrawing = function(callback){
	this.drawing = false;
	this.focused = false;
	$('#'+this.canvasIdentifier).removeClass('qtiShape-drawing');
	
	this.highlightShape(this.currentId, 1500);
	
	if(callback) callback(this.currentId, this.shapes[this.currentId], this);
}

/**
 * Called when need to interrumpt drawing
 */
qtiShapesEditClass.prototype.interruptDrawing = function(callback){
	if(callback) callback(this.currentId, this.shapes[this.currentId], this);
	this.currentId = 0;//reinitialize current id
}

qtiShapesEditClass.prototype.drawShape = function(startPoint, endPoint, shape){
	
	var returnValue = null;
	var raphaelObject = null;
	var shapeObject = null;
	
	if(!shape){
		shape = this.shape;
	}
	
	shape = shape.toLowerCase();
	//check if the drawing method exists:
	if(this.canvas){
		if(this.canvas[shape]){
			switch(shape){
				case 'circle':{
					var radius = Math.sqrt(Math.pow(endPoint.x-startPoint.x, 2)+Math.pow(endPoint.y-startPoint.y, 2))
					radius = _round(radius);
					raphaelObject = this.canvas.circle(startPoint.x, startPoint.y, radius);
					
					shapeObject = {
						type: 'circle',
						c: startPoint,
						r: radius
					}
					break;
				}
				case 'rect':{
					var corner = {
						x: Math.min(startPoint.x, endPoint.x),
						y: Math.min(startPoint.y, endPoint.y)
					};
					
					var width = Math.max(startPoint.x, endPoint.x) - corner.x;
					var height = Math.max(startPoint.y, endPoint.y) - corner.y;
					raphaelObject = this.canvas.rect(corner.x, corner.y, width, height);
					
					shapeObject = {
						type: 'rect',
						c: corner,
						w: width,
						h: height
					};
					break;
				}
				case 'ellipse':{
					var horizontalRadius = Math.abs(endPoint.x - startPoint.x);
					var verticalRadius = Math.abs(endPoint.y - startPoint.y);
					raphaelObject = this.canvas.ellipse(startPoint.x, startPoint.y, horizontalRadius, verticalRadius);
					
					shapeObject = {
						type: 'ellipse',
						c: startPoint,
						h: horizontalRadius,
						v: verticalRadius
					};
					break;
				}
				case 'path':{
					var svgPath = '';
					var thePath = []
					//get the previous points:
					if(this.path){
						thePath = [];
						this.path.push(endPoint); 
						
						for(var i=0; i<this.path.length; i++){
							var currentPoint = this.path[i];
							thePath.push(currentPoint);
							
							if(i==0){
								svgPath += 'M'+currentPoint.x+' '+currentPoint.y;
							}else{
								svgPath += 'L'+currentPoint.x+' '+currentPoint.y;
							}
						}
					}else{
						throw 'no path initiated';
					}
					
					shapeObject = {
						type: 'path',
						path: thePath
					}
					
					if(svgPath != ''){
						raphaelObject = this.canvas.path(svgPath);
					}
					
					break;
				}
			}
		}else if(shape == 'point'){
			
			shapeObject = {
				type: 'point',
				c: startPoint
			}
			
			svgPath = this.calculateCrossPath(startPoint);
			if(svgPath){
				raphaelObject = this.canvas.path(svgPath);
				raphaelObject.rotate(45);
			}
			
		}
	}
	
	if(raphaelObject){
		raphaelObject = this.styleShape(raphaelObject);
	}
	
	returnValue = {
		'raphaelObject': raphaelObject,
		'shapeObject': shapeObject
	}
	
	return returnValue;
}

qtiShapesEditClass.prototype.calculateCrossPath = function(startPoint){
	
	var pointSize = this.options.pointSize;
	var svgPath = '';
	
	if(startPoint, pointSize){
		
		var l = pointSize/2; 
		var L = pointSize*4;
		var x = parseFloat(startPoint.x);
		var y = parseFloat(startPoint.y);
		
		var offset = [
			[-(L+l),l],
			[-(L+l),-l],
			[-l,-l],
			[-l,-(l+L)],
			[l,-(l+L)],
			[l,-l],
			[l+L,-l],
			[l+L,l],
			[l,l],
			[l,l+L],
			[-l,l+L],
			[-l,l],
			[-(L+l),l]
		];
		
		svgPath = 'M'+eval(x+offset[0][0])+' '+eval(y+offset[0][1]);
		for(var i=1;i<offset.length;i++){
			svgPath += 'L'+eval(x+offset[i][0])+' '+eval(y+offset[i][1]);
		}
	}
	
	
	
	return svgPath;
}

qtiShapesEditClass.prototype.styleShape = function(raphaelObject){
	if(raphaelObject){
		raphaelObject.attr('fill', this.options.fill);
		raphaelObject.attr('stroke', this.options.stroke);
		raphaelObject.attr('fill-opacity', this.options.opacity);
		raphaelObject.toFront(); 
	}
	
	return raphaelObject
}

qtiShapesEditClass.prototype.hoverIn = function(id){
	if(this.shapes[id]){
		var raphaelObject = this.shapes[id].raphaelObject;
		if(raphaelObject){
			raphaelObject.attr('fill', this.options.fillHover);
			raphaelObject.attr('stroke', this.options.strokeHover);
			raphaelObject.attr('fill-opacity', this.options.opacityHover);
			this.shapes[id].raphaelObject = raphaelObject;
		}
	}
}

qtiShapesEditClass.prototype.hoverOut = function(id){
	if(this.shapes[id]){
		var raphaelObject = this.shapes[id].raphaelObject;
		if(raphaelObject){
			this.shapes[id].raphaelObject = this.styleShape(raphaelObject);
		}
	}
}

qtiShapesEditClass.prototype.removeShapeObj = function(id){
	if(id){
		if(this.shapes[id]){
			if(this.shapes[id].raphaelObject){
				if(this.shapes[id].raphaelObject.remove){
					this.shapes[id].raphaelObject.remove();
				}
				delete this.shapes[id];
			}
		}
	}else{
		//remove the current temporary shape object:
		if(this._shapeObj){
			if(this._shapeObj.remove){
				this._shapeObj.remove();
			}
			this._shapeObj = null;
		}
	}
	
}

//replace the targetted shapeObject (in the shapes array or current temp) with the new one
qtiShapesEditClass.prototype.setShapeObj = function(shapeObj, id){
	
	if(id){
		this.removeShapeObj(id);
		this.shapes[id] = shapeObj;
	}else{
		this.removeShapeObj();
		this._shapeObj = shapeObj;
	}
}

//id must be unique
qtiShapesEditClass.prototype.createShape = function(id, mode, options){
	
	var shapeObject = null;
	if(options.shape == 'poly'){
		options.shape = 'path';
	}
	
	//import to the sharing object format:
	switch(mode){
		case 'draw':{
			// var drawnShape = this.drawShape(options.startPoint, options.endPoint, options.shape);
			var drawnShape = this.importShapeFromCanvas(options.startPoint, options.endPoint, options.shape);
			shapeObject = {
				initMode: mode,
				type: options.shape,
				raphaelObject: drawnShape.raphaelObject,
				shapeObject: drawnShape.shapeObject
			};
			
			break;
		}
		case 'qti':{
			var qtiShape = this.importShapeFromQti(options.data, options.shape);
			shapeObject = {
				initMode: mode,
				type: options.shape,
				qtiObject: qtiShape.qtiObject,
				shapeObject: qtiShape.shapeObject
			};
			
			break;
		}
	}
	
	if(shapeObject){
		this.setShapeObj(shapeObject, id);
		return true;
	}
	
	return false;
}

qtiShapesEditClass.coordinatesToString = function(coords){
	var returnValue = '';
	var length = coords.length;
	for(var i=0; i<length; i++){
		if(i) returnValue += ',';
		returnValue += _round(coords[i]);
	}
	return returnValue;
}

//export shape object to qti compatible strings:
qtiShapesEditClass.prototype.exportShapeToQti = function(id, mode){

	var qtiCoords = '';
	
	//create qti coord string from itself or shapeObject
	if(this.shapes[id]){
		if(this.shapes[id].qtiObject){
			
			qtiCoords = this.shapes[id].qtiObject;
			
		}else if(this.shapes[id].shapeObject){
			
			var shapeObject = this.shapes[id].shapeObject;
			
			//processing required:
			switch(shapeObject.type){
				case 'circle':{
					if(shapeObject.c && shapeObject.r){
						qtiCoords = qtiShapesEditClass.coordinatesToString([shapeObject.c.x, shapeObject.c.y, shapeObject.r]);
					} 
					break;
				}
				case 'rect':{
					if(shapeObject.c && shapeObject.w && shapeObject.h){
						var rightX = shapeObject.c.x + shapeObject.w;
						var bottomY = shapeObject.c.y + shapeObject.h;
						qtiCoords = qtiShapesEditClass.coordinatesToString([shapeObject.c.x, shapeObject.c.y, rightX, bottomY]);
					}
					break;
				}
				case 'ellipse':{
					if(shapeObject.c && shapeObject.h && shapeObject.v){
						qtiCoords = qtiShapesEditClass.coordinatesToString([shapeObject.c.x, shapeObject.c.y, shapeObject.h, shapeObject.v]);
					}
					break;
				}
				case 'path':{
					if(shapeObject.path){
						for(var i=0; i<shapeObject.path.length; i++){
							if(i>0){
								qtiCoords += ',';
							}
							qtiCoords += shapeObject.path[i].x+','+shapeObject.path[i].y;
						}
					}
					break;
				}
				case 'point':{
					if(shapeObject.c){
						qtiCoords = qtiShapesEditClass.coordinatesToString([shapeObject.c.x, shapeObject.c.y]);
					} 
					break;
				}
			}
			
			if(qtiCoords){
				this.shapes[id].qtiObject = qtiCoords;
			}
		}
	}
	
	return qtiCoords;
}

//draw on the canvas from the shapeObject:
qtiShapesEditClass.prototype.exportShapeToCanvas = function(id, mode){
	//draw from raphaelObject or shapeObject
	var raphaelObject = null;
	
	if(this.shapes[id]){
		if(this.shapes[id].raphaelObject){
			//if already drawn, return it directly
			raphaelObject = this.shapes[id].raphaelObject;
		}else{
			//else draw it from the shapeObject if exists:
			if(this.shapes[id].shapeObject && this.canvas){
				var shapeObject = this.shapes[id].shapeObject;
				switch(shapeObject.type){
					case 'circle':{
						if(shapeObject.c && shapeObject.r){
							raphaelObject = this.canvas.circle(shapeObject.c.x, shapeObject.c.y, shapeObject.r);
						}
						break;
					}
					case 'rect':{
						if(shapeObject.c && shapeObject.w && shapeObject.h){
							raphaelObject = this.canvas.rect(shapeObject.c.x, shapeObject.c.y, shapeObject.w, shapeObject.h);
						}
						break;
					}
					case 'ellipse':{
						if(shapeObject.c && shapeObject.h && shapeObject.v){
							raphaelObject = this.canvas.ellipse(shapeObject.c.x, shapeObject.c.y, shapeObject.h, shapeObject.v);
						}
						break;
					}
					case 'path':{
						if(shapeObject.path){
							var svgPath = this.buildSVGpath(shapeObject.path);
							raphaelObject = this.canvas.path(svgPath);
						}
						break;
					}
					case 'point':{
						if(shapeObject.c){
							var svgPath = this.calculateCrossPath(shapeObject.c);
							if(svgPath){
								raphaelObject = this.canvas.path(svgPath);
								raphaelObject.rotate(45);
							}
							
						}
						break;
					}
				}
			}
			
			if(raphaelObject){
				raphaelObject = this.styleShape(raphaelObject);
				this.shapes[id].raphaelObject = raphaelObject;
			}
		}
	
		
	}
	return raphaelObject;
}

//create the shapeObject from the qti row data string
qtiShapesEditClass.prototype.importShapeFromQti = function(rowData, shape){
	
	var returnValue = null;
	var data = rowData.split(',');
	var qtiObject = null;
	var shapeObject = null;
	
	switch(shape){
		case 'circle':{
			if(data.length == 3){
				qtiObject = rowData;
				//warning! radius could be in %
				shapeObject = new Object();
				shapeObject = {
					type: 'circle',
					c: {
						x: data[0],
						y: data[1]
					},
					r: data[2]
				}
			}else{
				throw "wrong number of element found in circle row data";
			}
			break;
		}
		case 'rect':{
			if(data.length == 4){
				qtiObject = rowData;
				//warning! radius could be in %
				shapeObject = {
					type: 'rect',
					c: {
						x: data[0],
						y: data[1]
					},
					w: Math.abs(data[2]-data[0]),
					h: Math.abs(data[3]-data[1])
				}
			}else{
				throw "wrong number of element found in rect row data";
			}
			break;
		}
		case 'ellipse':{
			if(data.length == 4){
				qtiObject = rowData;
				//warning! radius could be in %
				shapeObject = {
					type: 'ellipse',
					c: {
						x: data[0],
						y: data[1]
					},
					h: data[2],
					v: data[3]
				}
			}else{
				throw "wrong number of element found in ellipse row data";
			}
			break;
		}
		case 'poly':
		case 'path':{
			if(data.length%2 == 0){
				qtiObject = rowData;
				var path = [];
				
				for(var i=0; i<data.length; i=i+2){
					path.push({
						x: data[i],
						y: data[i+1]
					});
				}
				
				//check if the final one is the same as the first:
				if(path.length>=2){
					if(path[0].x != path[path.length-1].x){
						path.push(path[0]);
					}
				}
				
				shapeObject = {
					type: 'path',
					path: path
				};
			}else{
				throw "no even number of element found in poly/path row data";
			}
			break;
		}
		case 'point':{
			if(data.length == 2){
				qtiObject = rowData;
				shapeObject = new Object();
				shapeObject = {
					type: 'point',
					c: {
						x: data[0],
						y: data[1]
					},
					r: this.options.pointSize
				}
			}else{
				throw "wrong number of element found in point row data";
			}
			break;
		}
	}

	returnValue = {
		'qtiObject': qtiObject,
		'shapeObject': shapeObject
	}

	return returnValue; 		
}

//create shape object and draw on raphael canvas from graphical data:
qtiShapesEditClass.prototype.importShapeFromCanvas = function(startPoint, endPoint, shape){
	//draw:
	return this.drawShape(startPoint, endPoint, shape);
}

qtiShapesEditClass.prototype.buildSVGpath = function(points){
	var svgPath = '';
	
	for(var i=0; i<points.length; i++){
		var currentPoint = points[i];
		
		if(i==0){
			svgPath += 'M'+currentPoint.x+' '+currentPoint.y;
		}else{
			svgPath += 'L'+currentPoint.x+' '+currentPoint.y;
		}
	}
	
	return svgPath;
}

qtiShapesEditClass.prototype.drawn = function(onDrawnFunction){
	if(this.options){
		if(onDrawnFunction){
			this.options.onDrawn = onDrawnFunction;
		}else{
			if(this.options.onDrawn) this.stopDrawing(this.options.onDrawn);
		}
	}
	
}
