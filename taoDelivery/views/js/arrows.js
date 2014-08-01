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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
// alert('arrowClass loaded');

//TODO: replace attribute 'name' by 'class'

processUtil = new Object();

processUtil.isset = function(object){

	if(typeof(object)=='undefined' || object===null){
		return false;
	}else{
		return true;
	}

}

ArrowClass = new Object();
ArrowClass.margin = 20;
ArrowClass.arrows = [];
ArrowClass.tempArrows = [];

ArrowClass.feedArrow = function(originId, targetId, targetObjectId, type, flex){
	//record the data to
	ArrowClass.arrows[originId] = {
		'id': originId,
		'targetObject': targetObjectId,
		'target': targetId,
		'type': type,
		'flex': flex
	}
}

ArrowClass.updateArrow = function(arrowId){
	var arrow = ArrowClass.arrows[arrowId];
	if(arrow){
		ArrowClass.arrows[arrowId] = ArrowClass.calculateArrow($('#'+arrowId), $('#'+arrow.target), arrow.type, new Array(), false);
		ArrowClass.redrawArrow(arrowId);
	}
}
ArrowClass.calculateArrow = function(point1, point2, type, flex, temp){
	
	//init values:
	//type in array('left','top','right');
	// alert("dsfdf"+type);
	// type ='top';
	
	var p1 = ArrowClass.getCenterCoordinate(point1);
	var p2 = ArrowClass.getCenterCoordinate(point2);
	var Dx = p2.x - p1.x;
	var Dy = p1.y - p2.y;//important
	var flexPointNumber = -1;
	
	if(!processUtil.isset(temp)){
		var temp = false;
	}
	
	//define default value by making distinction between temp and normal arrows
	if(!temp){
		if(!processUtil.isset(ArrowClass.arrows[point1.attr('id')]) && !temp){
			ArrowClass.arrows[point1.attr('id')] = new Array();
		}
		if(!processUtil.isset(flex)){
			if(processUtil.isset(ArrowClass.arrows[point1.attr('id')].flex) && !temp){
				flex = ArrowClass.arrows[point1.attr('id')].flex;
			}
		}
		if(!processUtil.isset(type)){
			if(processUtil.isset(ArrowClass.arrows[point1.attr('id')].type) && !temp){
				type = ArrowClass.arrows[point1.attr('id')].type;
			}
		}
	}else{
		// console.dir(ArrowClass.tempArrows[point1.attr('id')]);
		if(!processUtil.isset(ArrowClass.tempArrows[point1.attr('id')])){
			ArrowClass.tempArrows[point1.attr('id')] = new Array();
		}else{
			if(!processUtil.isset(flex)){
				if(processUtil.isset(ArrowClass.tempArrows[point1.attr('id')].flex)){
					flex = ArrowClass.tempArrows[point1.attr('id')].flex;
				}
			}
			if(!processUtil.isset(type)){
				if(processUtil.isset(ArrowClass.tempArrows[point1.attr('id')].type)){
					type = ArrowClass.tempArrows[point1.attr('id')].type;
				}
			}
		}
		
	}
	
	//if values still not found in arrow lists, set the default ones:  
	if(!processUtil.isset(flex)) var flex = new Array();
	if(!processUtil.isset(type)) var type =  'top';
	
	if(Dy>0 && type=='top'){
		flexPointNumber = 3;
	}else if(Dy<0 && type=='top'){
		flexPointNumber = 1;
	}else if( Dy<0 && ((Dx>0 && type=='left') || (Dx<0 && type=='right')) ){
		flexPointNumber = 0;
	}else{
		flexPointNumber = 2;
	}
	// alert("flexPointNb: "+flexPointNumber+ ", Dx: "+ Dx+ ", Dy: "+ Dy);
	
	
	var arrow = new Array();
	var flexPoints = new Array();
	arrow[0] = {x:p1.x, y:p1.y};
	switch(flexPointNumber){
		case 0:{
			arrow[1] = {x:p1.x, y:p2.y}; 
			arrow[2] = {x:p2.x, y:p2.y}; 
			break;
			}
		case 1:{
			//check the value flex1 for the arrow exists:
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				//if not calculate flex1: (delta2)
				flex1 = (p2.y-p1.y)/2;
			}
			flexPoints[1] = flex1;
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p2.x, y:p1.y + flex1};
			arrow[3] = {x:p2.x, y:p2.y}; 		
			break;
		}
		case 2:{
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				//calculate default value
				if(Dy>0){
					flex1 = ArrowClass.margin;
				}else{
					flex1 = (p2.y-p1.y)/2 - point2.height()/2;
				}
			}
			flexPoints[1] = flex1;
			
			if(isset(flex[2])){
				flex2 = flex[2];
			}else{
				if(Dx>0){
					if(type=='right'){
						flex2 = (p2.x + ArrowClass.margin) - p1.x;
					}else{
						flex2 = (p2.x - p1.x)/2 - point1.width()/2;//warning: division by 0!
					}
				}else{
					if(type=='right'){
						flex2 = (p2.x - p1.x)/2 - point1.width()/2;
					}else{
						flex2 = (p2.x - ArrowClass.margin) - p1.x;
					}
				}
			}
			flexPoints[2] = flex2;	
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p1.x+flex2, y:p1.y + flex1};
			arrow[3] = {x:p1.x+flex2, y:p2.y};
			arrow[4] = {x:p2.x, y:p2.y}; 			
			break;
		}
		case 3:{
			if(isset(flex[1])){
				flex1 = flex[1];
			}else{
				flex1 = ArrowClass.margin;
			}
			if(isset(flex[2])){
				flex2 = flex[2];
			}else{
				flex2 = (p2.x-p1.x)/2;
			}
			if(isset(flex[3])){
				flex3 = flex[3];
			}else{
				flex3 = (-1) * ArrowClass.margin;
			}
			flexPoints[1] = flex1;
			flexPoints[2] = flex2;
			flexPoints[3] = flex3;
			
			arrow[1] = {x:p1.x, y:p1.y + flex1}; 
			arrow[2] = {x:p1.x+flex2, y:p1.y + flex1};
			arrow[3] = {x:p1.x+flex2, y:p2.y+flex3};
			arrow[4] = {x:p2.x, y:p2.y+flex3};
			arrow[5] = {x:p2.x, y:p2.y};			
			break;
		}
	}
	
	//modify the global array table
	// ArrowClass.arrows[point1.attr('id')] = {
		// 'target': point2.attr('id'),
		// 'coord': arrow,
		// 'type': type,
		// 'flex': flexPoints
	// }
	
	
	return {
			'targetObject': ArrowClass.getTargetFromId(point2.attr('id')),
			'target': point2.attr('id'),
			'coord': arrow,
			'type': type,
			'flex': flexPoints
		};
	// console.log('test',point1.attr('id'));
	//console.log('x1',p1.x);
	//console.log('y1',p1.y);
	//console.log('x2',p2.x);
	//console.log('y2',p2.y);
	//console.log('Dx',Dx);
	//console.log('Dy',Dy);
	//console.log('flexPt', flexPointNumber);
	//console.log('flex1', flex1);
	//console.log('flex2', flex2);
	// console.log('flex3', flex3);
	//console.dir(arrows);
}

ArrowClass.getTargetFromId = function(destinationId){
	var targetObject = 'undefined';
	
	var index = destinationId.lastIndexOf('_pos_');
	if(index>0){
		if(destinationId.substring(0,9) == 'activity_'){
			targetObject = destinationId.substring(9,index);
		}else if(destinationId.substring(0,10) == 'connector_'){
			targetObject = destinationId.substring(10,index);
		}
	}
	
	return targetObject;
}

function createArrow(origineId, position){

	//initialize the arrow:
	var left = 0;
	var top = 0;
	if(isset(position.left)){
		left = position.left;
	}
	if(isset(position.top)){
		top = position.top;
	}
	
	//the origin of an arrow is always the bottom of the origine a connector
	
	//add the arrow tip element
	var tipId = origineId + '_tip';
	var elementTip = $('<div id="'+tipId+'"></div>');//put connector id here instead
	elementTip.addClass('diagram_arrow_tip');
	elementTip.css('position', 'absolute');
	elementTip.css('left', Math.round(left)+'px');
	elementTip.css('top', Math.round(top)+'px');
	elementTip.appendTo(canvas);
	
	//calculate the initial position & draw it
	calculateArrow($('#'+origineId), elementTip, 'top', null);//default value of the 'type' set to 'top' ...
	drawArrow(origineId, {
		container: canvas,
		arrowWidth: 1
	});
	
	//transform to draggable
	$('#'+elementTip.attr('id')).draggable({
		snap: '.diagram_activity_droppable',
		snapMode: 'inner',
		drag: function(event, ui){
			
			// var position = $(this).position();
			// $("#message").html("<p> left: "+position.left+", top: "+position.top+"</p>");
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			
			var arrow = arrows[arrowName];
			
			//TODO edit 'type' at the same time:
			
			removeArrow(arrowName);
			calculateArrow($('#'+arrowName), $(this), arrow.type, null);
			drawArrow(arrowName, {
				container: canvas,
				arrowWidth: 1
			});
		},
		containment: canvas,
		stop: function(event, ui){
			var id = $(this).attr('id');
			var arrowName = id.substring(0,id.indexOf('_tip'));
			getDraggableFlexPoints(arrowName);
		}
	});
	
}
ArrowClass.redrawArrow = function(activityId, temp, options){
	if(temp){
		ArrowClass.removeArrow(activityId, false, true);
		ArrowClass.drawArrow(activityId, {
			container: ActivityDiagramClass.canvas,
			arrowWidth: 2,
			temp: true
		});
	}else{
		ArrowClass.removeArrow(activityId, false);
		ArrowClass.drawArrow(activityId, {
			container: ActivityDiagramClass.canvas,
			arrowWidth: 2
		});
		
		//manage options
		var setMenuHandler = false;
		if(options){
			if(options.setMenuHandler){
				setMenuHandler = options.setMenuHandler;
			}
		}
		
		if(setMenuHandler){
			ActivityDiagramClass.setArrowMenuHandler(activityId);
		}
	}
}

ArrowClass.drawArrow = function(arrowName, options){
	
	
	if(!isset(options)){
		throw 'no options set';
	}
	if(options.temp){
		if(!isset(ArrowClass.tempArrows[arrowName].coord)){
			throw 'the temporary arrow does not exist';
		}
		var p = ArrowClass.tempArrows[arrowName].coord;
	}else{
		if(!isset(ArrowClass.arrows[arrowName].coord)){
			throw 'the arrow does not exist';
		}
		var p = ArrowClass.arrows[arrowName].coord;
	}
	
	options.name = arrowName;
	if(isset(p[0])&&isset(p[1])){
		options.index = 1;
		ArrowClass.drawVerticalLine(p[0], p[1], options);
		if(isset(p[2])){
			options.index = 2;
			ArrowClass.drawHorizontalLine(p[1], p[2], options);
			if(isset(p[3])){
				options.index = 3;
				ArrowClass.drawVerticalLine(p[2], p[3], options);
				if(isset(p[4])){
					options.index = 4;
					ArrowClass.drawHorizontalLine(p[3], p[4], options);
					if(isset(p[5])){
						options.index = 5;
						ArrowClass.drawVerticalLine(p[4], p[5], options);
					}
				}
			}
		}
		
		//draw the extremity: the tip (a picture?)
	}
	
}

ArrowClass.drawVerticalLine = function(p1, p2, options){
	var arrowWidth = 0;
	if(options.arrowWidth){
		arrowWidth = options.arrowWidth; 
	}else{
		arrowWidth = 2;
	}
	
	var width = arrowWidth;
	var height = Math.abs(p1.y - p2.y);
	var left =  p1.x - arrowWidth/2;//p[0].x  == p[0].y 
	var top = Math.min(p1.y,p2.y);
	var classes = new Array();
	if(options.temp){
		classes.push('temp_arrow');
	}
	
	ArrowClass.drawArrowPart(1,left,top,width,height,options.container,options.name,options.index,classes);
}

ArrowClass.drawHorizontalLine = function(p1, p2, options){
	var arrowWidth = 0;
	if(options.arrowWidth){
		arrowWidth = options.arrowWidth; 
	}else{
		arrowWidth = 2;
	}
	
	var width = Math.abs(p2.x-p1.x);
	var height = arrowWidth;
	var left = Math.min(p1.x, p2.x);
	var top = p1.y - arrowWidth/2;
	var classes = new Array();
	if(options.temp){
		classes.push('temp_arrow');
	}
	
	ArrowClass.drawArrowPart(1,left,top,width,height,options.container,options.name,options.index,classes);
}

ArrowClass.drawArrowPart = function(border,left,top,width,height,container,name,arrowPartIndex,classes){
	
	if(container && name){
	//"#"+arrowName+"_arrowPart_"+arrowPartIndex
		var borderStr = Math.round(border)+'px '+'solid'+' '+'red';
		var element = $('<div id="'+name+'_arrowPart_'+arrowPartIndex+'"></div>');
		element.addClass(name);
		element.addClass('arrow');
		if(classes.length){
			for(var i=0;i<classes.length;i++){
				element.addClass(classes[i]);
			}
		}
		// element.css('border', borderStr);//no border
		element.css('position', 'absolute');
		element.css('background-color', 'black');
		element.css('left', Math.round(left)+'px');
		element.css('top', Math.round(top)+'px');
		element.css('width', Math.round(width)+'px');
		element.css('height', Math.round(height)+'px');
		
		//console.log('left:',element.css('left'));
		//console.log('top:',element.css('top'));
		//console.log('w:',element.css('width'));
		//console.log('h:',element.css('height'));
		// console.log('x2',p2.x);
		// console.log('y2',p2.y);
	
		element.appendTo(container);
	}
}

ArrowClass.removeArrow = function(name, complete, temp){
	if(!processUtil.isset(complete)){
		complete = true;
	}
	if(!processUtil.isset(temp)){
		temp = false;
	}
	// console.log('complete', complete);
	// console.log('temp', temp);
	
	if(temp){
		if(complete){
			ArrowClass.tempArrows[name] = null;
		}
		$(".temp_arrow."+name).remove();
	}else{
		if(complete){
			ArrowClass.arrows[name] = null;
		}
		$("."+name).remove();
	}
}

//draggable points can only exists in temp arrow
ArrowClass.getDraggableFlexPoints = function(tempArrowName){
	var arrow = ArrowClass.tempArrows[tempArrowName];
	
	//get the postion of flex points, and transform them into draggable object:
	for(i=1;i<=arrow.flex.length;i++){
		
		if(isset(arrow.flex[i])){
			if(i%2){
				//vertical only:
				authorizedAxis = 'y';
			}else{
				//horizontal only:
				authorizedAxis = 'x';
			}
			
			var arrowPartIndex = i + 1 ;
			var arrowPartId = tempArrowName + "_arrowPart_"+arrowPartIndex;
			var dragHandleId = arrowPartId + '_handle';
			
			//create the handle in the middle:
			var handleElement = $('<div id="'+dragHandleId+'"/>');
			handleElement.addClass(tempArrowName);
			handleElement.addClass('flex_point');
			// var borderStr = '1px '+'solid'+' '+'green';
			// handleElement.css('border', borderStr);
			// handleElement.css('width', '5px');
			// handleElement.css('height', '5px');
			handleElement.appendTo("#"+arrowPartId);
			$('#'+dragHandleId).position({
				of: "#"+arrowPartId,
				my: "center",
				at: "center"
			});
			
			
			//get the element and transform it into a draggable (with constraint):
			$("#"+arrowPartId).draggable({
				axis: authorizedAxis,
				opacity: 0.7,
				helper: 'clone',
				handle: "#"+dragHandleId,
				start: function(event, ui){
					// console.log($(this).draggable('option', 'handle'));
				},
				drag: function(event, ui){
										
				},
				containment: ActivityDiagramClass.canvas,
				stop: function(event, ui){
					
					var offset = 0;
					if($(this).draggable('option', 'axis') == 'x'){
						offset = ui.position.left - ui.originalPosition.left;
					}else if($(this).draggable('option', 'axis') == 'y'){
						offset = ui.position.top - ui.originalPosition.top;
					}else{
						return false;
					}
					
					//get value of flex points:
					var flexPoints = new Array();
					var id = $(this).attr('id');
					var tempIndex = parseInt(id.substr(id.lastIndexOf("arrowPart_")+10)) - 1;
					
					// arrowNameTemp = $(this).attr('name');
					var arrowNameTemp = id.substring(0,id.indexOf('_arrowPart_'));
					// console.log(arrowNameTemp);
					var arrowTemp = ArrowClass.tempArrows[arrowNameTemp];
					var flexPoints = ArrowClass.editArrowFlex(arrowNameTemp, tempIndex, offset);
					
					ArrowClass.tempArrows[arrowNameTemp] = ArrowClass.calculateArrow($("#"+arrowNameTemp), $("#"+arrowTemp.target), arrowTemp.type, flexPoints, true);
					ArrowClass.tempArrows[arrowNameTemp].actualTarget = arrowTemp.actualTarget;
					ArrowClass.redrawArrow(arrowNameTemp, true);
					ArrowClass.getDraggableFlexPoints(arrowNameTemp);
				}

			});
			
			
			
		}else{
			break;
		}
	}
	//clear momemory:
	arrowName = '';
	authorizedAxis = '';
}

ArrowClass.getCenterCoordinate = function(element){
	
	if(!element.length){
		throw 'the element "'+element.attr('id')+'" do not exists';
		return null;
	}
	var canvasElt = $(ArrowClass.canvas);
	if(!canvasElt.length){
		throw 'no canvas defined';
		return null
	}
	
	var position = element.offset();
	var canvasOffset = canvasElt.offset();
	
	x = (position.left-canvasOffset.left+ActivityDiagramClass.scrollLeft) + element.width()/2;
	y = (position.top-canvasOffset.top+ActivityDiagramClass.scrollTop) + element.height()/2;
	// console.log('ActivityDiagramClass.scrollLeft', ActivityDiagramClass.scrollLeft);
	// console.log('ActivityDiagramClass.scrollTop', ActivityDiagramClass.scrollTop);
	
	// console.log('Cx',element.width());
	// console.log('Cy',element.height());
	// console.log('x', x);
	// console.log('y', y);
	
	return {x:x, y:y};
}

function isset(object){
	if(typeof(object)=='undefined' || object===null){
		return false;
	}else{
		return true;
	}
}

//only on temp arrows
ArrowClass.editArrowFlex = function(arrowName, flexPosition, offset){
	
	var flexPoints = new Array();
	
	if(isset(ArrowClass.tempArrows[arrowName])){
		var arrow = ArrowClass.tempArrows[arrowName];
		//get value of flex points:
		
		for(i=1;i<=arrow.flex.length;i++){
			
			if(isset(arrow.flex[i])){
				
				if(i == flexPosition){
					//TODO: define allowed range of value for offset
					
					if(i == 1){
						//the first flex point cannot be above the point of origin:
						if(arrow.flex[i]+offset <= 0){
							continue;//do not modify it
						}
					}else if(i == 2){
						target = ArrowClass.getCenterCoordinate($('#'+arrow.target));
						origin = ArrowClass.getCenterCoordinate($('#'+arrowName));
						Dx = (target.x + offset) - origin.x;
						// console.log(Dx);
						// console.dir(target);
						// console.dir(origin);
						if(Dx > 0 && arrow.type=='left'){
							continue;
						}
						if(Dx < 0 && arrow.type=='right'){
							continue;
						}
					}else if(i == 3){
						if(arrow.flex[i]+offset >= 0){
							continue;
						}
					}
					flexPoints[i] = arrow.flex[i] + offset;// + or -
					
				}else{
					flexPoints[i] = arrow.flex[i];
				}
				
			}else{
				break;
			}
			
		}
		
	}
	
	//immediately followed by calculateArrow and drawArrow;
	return flexPoints;
}

ArrowClass.saveTemporaryArrowToReal = function(arrowId){
	// save the temporay arrow data into the actual arrows array:
	if(ArrowClass.tempArrows[arrowId]){
		
		var tempArrow = ArrowClass.tempArrows[arrowId];
		ArrowClass.arrows[arrowId] = tempArrow;
		
		//set the real target element (not the deleted arrow tip)
		ArrowClass.arrows[arrowId].target = tempArrow.actualTarget;
		// delete ArrowClass.arrows[arrowId].actualTarget;
		
		//delete the temp arrows and draw the actual one:
		ModeArrowLink.removeTempArrow(arrowId);
		ArrowClass.drawArrow(arrowId, {
			container: ActivityDiagramClass.canvas,
			arrowWidth: 2
		});
		// console.dir(ArrowClass.arrows);
		ActivityDiagramClass.setArrowMenuHandler(arrowId);
	}
}

/*
ArrowClass.editArrowType = function(arrowName, newType, temp){
	//newType in left, top, right
	
		arrowTemp = ArrowClass.arrows[arrowName];
		if(isset(arrowTemp)){
			// calculateArrow($("#"+arrowNameTemp), $("#"+arrowTemp.target), newType);
			ArrowClass.arrows[arrowName].type = newType;
		}
	}else{
		arrowTemp = ArrowClass.tempArrows[arrowName];
		if(isset(arrowTemp)){
			// calculateArrow($("#"+arrowNameTemp), $("#"+arrowTemp.target), newType);
			ArrowClass.tempArrows[arrowName].type = newType;
		}
	}
	
	
	// console.dir(arrowTemp);
	//do not forget to draw it when done;
}*/

ArrowClass.getArrow = function(arrowName, temp){
	
	var arrow = null;
	
	if(!processUtil.isset(temp)){
		temp = false;
	}
	if(!temp){
		arrowTemp = ArrowClass.arrows[arrowName];
		if(processUtil.isset(arrowTemp)){
			return arrowTemp;
		}
	}else{
		arrowTemp = ArrowClass.tempArrows[arrowName];
		if(processUtil.isset(arrowTemp)){
			return arrowTemp;
		}
	}
	return arrow;
}

function editArrowClass(){

}
