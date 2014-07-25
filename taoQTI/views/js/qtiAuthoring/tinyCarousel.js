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
 */
tinyCarousel.instances = [];
function tinyCarousel(container, content, next, prev, options){

	this.current = 0;
	this.options = options;
	this.container = container;
	this.content = content;

	var $divs = $(this.content+' div');
	this.total = $divs.length;
	var $refElt = $($divs[0]);
	this.stepWidth = parseInt($refElt.innerWidth());
	this.contentWidth = this.total * this.stepWidth;

	$(this.content).width(this.contentWidth);
	$(this.container).css('overflow', 'hidden');

	this.nextButton = next;
	this.prevButton = prev;
	var self = this;
	$(this.prevButton).click(function(){
		self.prev();
	}).css('visibility', 'hidden');

	$(this.nextButton).click(function(){
		self.next();
	}).css('visibility', 'hidden');

	this.init = function(){
		this.contentWidth = $(this.content).innerWidth();
		this.containerWidth = $(this.container).innerWidth();
		if(this.contentWidth && this.containerWidth){
			if(!this.stepWidth) this.stepWidth = this.contentWidth/this.total;
			this.updateButtonVisibility();
		}
	}

	this.prev = function(){
		if(this.current>0){
			this.current--;
			this.updateButtonVisibility();

			if(this.stepWidth){
				var currentMarginLeft = parseInt($(this.content).css('margin-left'));
				if(!currentMarginLeft) currentMarginLeft = 0;
				var newMarginLeft = currentMarginLeft + this.stepWidth;
				$(this.content).css('margin-left', parseInt(newMarginLeft)+'px');
			}

		}
	}

	this.next = function(){
		if(this.current<this.total){
			this.current++;
			this.updateButtonVisibility();

			if(this.stepWidth){
				var currentMarginLeft = parseInt($(this.content).css('margin-left'));
				if(!currentMarginLeft) currentMarginLeft = 0;
				var newMarginLeft = currentMarginLeft - this.stepWidth;
				$(this.content).css('margin-left', parseInt(newMarginLeft)+'px');
			}

		}
	}

	this.updateButtonVisibility = function(){

		var extraSteps = ($(this.content).innerWidth()-$(this.container).innerWidth())/this.stepWidth;

		if(this.current >= extraSteps){
			$(this.nextButton).css('visibility', 'hidden');
		}else{
			$(this.nextButton).css('visibility', 'visible');
		}

		if(this.current <= 0){
			$(this.prevButton).css('visibility', 'hidden');
		}else{
			$(this.prevButton).css('visibility', 'visible');
		}
	}

	this.init();
	tinyCarousel.instances[this.container] = this;
}

tinyCarousel.prototype.update = function(){
	//the container size might change, but content not
	this.updateButtonVisibility();
}