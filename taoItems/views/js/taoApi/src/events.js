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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
/**
 * TAO API events utilities.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery >= 1.4.0 {@link http://www.jquery.com}
 * 
 * @see NewarX#Core
 */

/**
 *  
 * @class EventTracer
 * @property {Object} [options] 
 */
function EventTracer (options){
	
	//keep the ref of the current instance for scopes traversing
	var _this = this;
	
	/**
	 * array of events arrays
	 * @fieldOf EventTracer
	 * @type {Array}
	 */
	this.eventPool = new Array();// 
	
	/**
	 * array of strings
	 * @fieldOf EventTracer
	 * @type {Array}
	 */
	this.eventsToBeSend = new  Array();
	
	/**
	 * The tracer common options
	 * @fieldOf EventTracer
	 * @type {Object}
	 */
	this.opts = {
		POOL_SIZE : 500, // number of events to cache before sending
		MIN_POOL_SIZE : 200,
		MAX_POOL_SIZE : 5000,
		time_limit_for_ajax_request : 2000,
		eventsToBeSendCursor : -1,
		ctrlPressed : false,
		altPressed : false
	};
	
	//extends the options on the object construction
	if(options != null && options != undefined){
		$.extend(this.opts, options);
	}
	
	
	/**
	 * the list of events to be catched
	 * @fieldOf EventTracer
	 * @type {Object}
	 */
	this.EVENTS_TO_CATCH = new Object();
	
	/**
	 * the list of attributes to be catched
	 * @fieldOf EventTracer
	 * @type {Object}
	 */
	this.ATTRIBUTES_TO_CATCH = new Array();

	/**
	 * The parameters defining how and where to load the events list to catch
	 * @fieldOf EventTracer
	 * @type {Object}
	 */
	this.sourceService = {
		type:	'sync',										// (sync | manual)
		data:	null,										//if type is manual, contains the data in JSON, else it should be null
		url:	'/taoDelivery/ResultDelivery/getEvents',		//the url sending the events list
		params: {},											//the common parameters to send to the service
		method: 'post',										//sending method
		format: 'json'										//the response format, now ONLY JSON is supported
	};									
	
	/**
	 * The parameters defining how and where to send the events
	 * @fieldOf EventTracer
	 * @type {Object}
	 */
	this.destinationService = {
		url:	'/taoDelivery/ResultDelivery/traceEvents',			//the URL where to send the events
		params: {},											//the common parameters to send to the service
		method: 'post',										//sending method
		format: 'json'										//the response format, now ONLY JSON is supported
	};

	/**
	 * Initialize the service interface for the source service: 
	 * how and where we retrieve the events to catch
	 * @methodOf EventTracer
	 * @param {Object} environment
	 */
	this.initSourceService = function(environment){
		
		//define the source service
		if($.isPlainObject(environment)){
			
			if($.inArray(environment.type, ['manual','sync']) > -1){
				
				this.sourceService.type = environment.type;
				
				//manual behaviour
				if(this.sourceService.type == 'manual' && $.isPlainObject(environment.data)){
					this.sourceService.data = environment.data;
				}
				else{ 	//remote behaviour
			
					if(source.url){
						if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
							this.sourceService.url = environment.url;		//set url
						}
					}
					//ADD parameters
					if($.isPlainObject(environment.params)){	
						for(key in environment.params){
							if(isScalar(environment.params[key])){
								this.sourceService.params[key] = environment.params[key]; 
							}
						}
					}
					if(environment.method){
						if(/^get|post$/i.test(environment.method)){
							this.sourceService.method = environment.method;
						}
					}
				}
			}
		}
		
		//we load now the events to catch
		
		//we load it manually by calling directly the method with the data
		if(this.sourceService.type == 'manual' && this.sourceService.data != null){
			this.EVENTS_TO_CATCH = this.setEventsToCatch(this.sourceService.data);
		}
		
		//we call the remote service 
		if(this.sourceService.type == 'sync' && this.sourceService.url != ''){
			received = $.parseJSON($.ajax({
				async		: false,
				url  		: this.sourceService.url,
				data 		: this.sourceService.params,
				type 		: this.sourceService.method
			}).responseText);
			if(received){		
				this.EVENTS_TO_CATCH = this.setEventsToCatch(received);
			}
		}	
		
		//we bind the events to be observed in the item
		if(this.EVENTS_TO_CATCH.bubbling != undefined){
			this.bind_platform();
		}
	};
	
	/**
	 * Initialize the service interface forthe destination service:  
	 * how and where we send the catched events
	 * @methodOf EventTracer
	 * @param {Object} environment
	 */
	this.initDestinationService = function(environment){
		if($.isPlainObject(environment)){
			if(environment.url){
				if(/(\/[-a-z\d%_.~+]*)*(\?[;&a-z\d%_.~+=-]*)?(\#[-a-z\d_]*)?$/.test(environment.url)){	//test url
					this.destinationService.url = environment.url;		//set url
				}
			}
			//ADD parameters
			if($.isPlainObject(environment.params)){	
				for(key in environment.params){
					if(isScalar(environment.params[key])){
						this.destinationService.params[key] = environment.params[key]; 
					}
				}
			}
			if(environment.method){
				if(/^get|post$/i.test(environment.method)){
					this.destinationService.method = environment.method;
				}
			}
		}
	};
	
	/**
	* @description record events of interaction between interviewee and the test
	* @methodOf EventTracer
	* @param {Object} data event type list
	* @returns {Object} the events to catch
	*/
	this.setEventsToCatch = function (data)
	{
		// retreive the list of events to catch or not to catch
		
		if (data.type.length > 0)
		{
			var EVENTS_TO_CATCH = {bubbling:[],nonBubbling:[]};
			if (data.type == 'catch')
			{
				for (i in data.list)
				{
					if ($.inArray(i,['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout']) > -1)//if is bubbling event
					{
						EVENTS_TO_CATCH.bubbling.push(i);
					}
					else
					{
						EVENTS_TO_CATCH.nonBubbling.push(i);// else non bubbling event
					}
					this.ATTRIBUTES_TO_CATCH[i] = data.list[i];
				}
			}
			else
			{
				// no catch
				EVENTS_TO_CATCH = {bubbling:['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout'], nonBubbling:['blur', 'focus', 'load', 'resize', 'scroll', 'keyup', 'keydown', 'keypress', 'unload', 'beforeunload', 'select', 'submit']};
				for (i in data.list)
				{
					remove_array(data.list[i].event,EVENTS_TO_CATCH.bubbling);
					remove_array(data.list[i].event,EVENTS_TO_CATCH.nonBubbling);
				}
			}
		}
		else
		{
			EVENTS_TO_CATCH = {bubbling:['click', 'dblclick', 'change', 'submit', 'select', 'mousedown', 'mouseup', 'mouseenter', 'mousemove', 'mouseout'], nonBubbling:['blur', 'focus', 'load', 'resize', 'scroll', 'keyup', 'keydown', 'keypress', 'unload', 'beforeunload', 'select', 'submit']};
		}
		return EVENTS_TO_CATCH;
	};
	
	/**
	* @description bind platform events
	* @methodOf EventTracer
	*/
	this.bind_platform = function()
	{
		// for non bubbling events, link them to all the listened element
		// it is still useful to use delegation since it will remains much less listeners in the memory (just 1 instead of #numberOfElements)
		$('body').bindDom(this);

		// for bubbling events
		$('body').bind(this.EVENTS_TO_CATCH.bubbling.join(' ') , this.eventStation);
	};
	
	/**
	 * @description unbind platform events
	 * @methodOf EventTracer
	 */
	this.unbind_platform = function()
	{
		$('body').unbind(EVENTS_TO_CATCH.bubbling.join(' ') , this.eventStation);
		$('body').unBindDom(this);
	};
	
	
	/**
	 * @description set all information from the event to the pLoad
	 * @methodOf EventTracer
	 * @param {event} e dom event triggered
	 * @param {Object} pload callback function called when 'ok' clicked
	 */
	this.describeEvent = function(e,pload)
	{
		if (e.target && (typeof(e.target['value']) != 'undefined') && (e.target['value'] != -1) && (e.target['value'] != ''))
		{
			pload['value'] = e.target['value'];
		}
		// get everything about the event
		for (var i in e)
		{
			if ((typeof(e[i]) != 'undefined') && (typeof(e[i]) != 'object') && (typeof(e[i]) != 'function') && (e[i] != ''))
			{
				if ((i != 'cancelable') && (i != 'contentEditable') && (i != 'cancelable') && (i != 'bubbles') && (i.substr(0,6) != 'jQuery'))
				{
					pload[i] = e[i];
				}
			}
		}
	};


	/**
	* @description set all information from the target dom element to the pLoad
	* @methodOf EventTracer
	* @param {event} e dom event triggered
	* @param {Object} pload callback function called when 'ok' clicked
	*/
	this.describeElement = function(e,pload)
	{
		// take everything except useless attributes
		for (var i in e.target)
		{
			try
			{
				if (( (typeof(e.target[i]) == 'string') && (e.target[i] != '') ) | (typeof(e.target[i]) == 'number'))
				{
					if ( (!in_array(i,position_pload_array)) && (!in_array(i,ignored_pload_element_array)) && (i.substr(0,6) != 'jQuery') )
					{
						pload[i] = ''+e.target[i];
					}
				}
			}
			catch(e){}
		}

		if (typeof(e.target.nodeName) != 'undefined')
		{
			switch(e.target.nodeName.toLowerCase())
			{
				case 'select':
				{
					pload['value'] = $(e.target).val();
					if (typeof(pload['value']) == 'array')
					{
						pload['value'] = pload['value'].join('|');
					}
					break;
				}
				case 'textarea':
				{
					pload['value'] = $(e.target).val();
					
					break;
				}
				case 'input':
				{
					pload['value'] = $(e.target).val();
					break;
				}
				case 'html':// case of iframe in design mode, equivalent of a textarea but with html
				{
					if (e.target.ownerDocument.designMode == 'on')
					{
						pload['text'] = $(e.target).contents('body').html();
					}
					break;
				}
			}
		}
	};

	/**
	* @description set wanted information from the event to the pLoad
	* @methodOf EventTracer
	* @param {event} e dom event triggered
	* @param {Object} pload callback function called when 'ok' clicked
	*/
	this.setEventParameters = function (e,pload)
	{
		for (var i in this.ATTRIBUTES_TO_CATCH[e.type])
		{
			if (typeof(e[this.ATTRIBUTES_TO_CATCH[e.type][i]]) != 'undefined')
			{
				pload[this.ATTRIBUTES_TO_CATCH[e.type][i]] = e[this.ATTRIBUTES_TO_CATCH[e.type][i]];
			}
			else
			{
				if (typeof(e.target[this.ATTRIBUTES_TO_CATCH[e.type][i]]) != 'undefined')
				{
					pload[this.ATTRIBUTES_TO_CATCH[e.type][i]] = e.target[this.ATTRIBUTES_TO_CATCH[e.type][i]];
				}
			}
		}
	};


	/**
	 * @description return true if the event passed is a business event
	 * @methodOf EventTracer
	 * @param {event} e dom event triggered
	 * @returns {boolean}
	 */
	this.hooks = function(e){
		return (e.name == 'BUSINESS');
	};

	/**
	 * @description controler that send events to feedtrace
	 * @methodOf EventTracer
	 * @param {event} e dom event triggered
	 */
	this.eventStation = function (e){
		var keyCode = e.keyCode ? e.keyCode : e.charCode;
		if (e.type == 'keypress')// kill f4,f5,ctrl+r,s,t,n,u,p,o alt+tab,left and right arrow, right and left window key
		{
			try
			{
				if ( (typeof(keyCode) != 'undefined') && ((keyCode == 116) | (keyCode == 115) | ((e.ctrlKey)&&((keyCode ==  114)|(keyCode ==  115)|(keyCode ==  116)|(keyCode ==  112)|(keyCode ==  110)|(keyCode ==  111)|(keyCode ==  79)) ) | ((e.altKey)&&(keyCode == 9 )) | (keyCode == 91) | (keyCode == 92)| (keyCode == 37)| (keyCode == 39) ) )
				{
					e.preventDefault();
					return false;
				}
			}
			catch(e){}
		}

		var target_tag = e.target.nodeName ? e.target.nodeName.toLowerCase():e.target.type;
		var idElement;

		if ((e.target.id) && (e.target.id.length > 0))
		{
			idElement = e.target.id;
		}
		else
		{
			idElement = 'noID';
		}
		var pload = {'id' : idElement};

		if ((typeof(this.ATTRIBUTES_TO_CATCH)!= 'undefined') && (typeof(this.ATTRIBUTES_TO_CATCH[e.type])!= 'undefined') && (this.ATTRIBUTES_TO_CATCH[e.type].length > 0))
		{
			this.setEventParameters(e,pload);
		}
		else
		{
			if (typeof(this.describeEvent) != 'undefined')
			{
				this.describeEvent(e,pload);
			}
			if (typeof(this.describeElement) != 'undefined')
			{
				this.describeElement(e,pload);
			}
		}
		
		_this.feedTrace(target_tag, e.type, e.timeStamp, pload);
	};


	/**
	 * @description in the API to allow the unit creator to send events himself to the event log record events of interaction between interviewee and the test
	 * @example feedTrace('BUSINESS','start_drawing',getGlobalTime(), {'unitTime':getUnitTime()});
	 * @methodOf EventTracer
	 * @param {String} target_tag element type receiving the event.
	 * @param {String} event_type type of event being catched
	 * @param {Object} pLoad object containing various information about the event. you may put whatever you need in it.
	 */
	this.feedTrace = function (target_tag,event_type,time, pLoad)
	{
		var send_right_now = false;
		var event = '{"name":"'+target_tag+'","type":"'+event_type+'","time":"'+time+'"';

		
		if (typeof(pLoad)=='string')
		{
			event = event+',"pLoad":"'+pLoad+'"';
		}
		else
		{
			for (var prop_name in pLoad)
			{
				event = event+',"'+prop_name+'":"'+pLoad[prop_name]+'"';
			}
		}
		event = event+'}';
		
		if (typeof(this.hooks) != "undefined")
		{
			send_right_now = this.hooks($.parseJSON(event));
		}
		
		this.eventPool.push(event);
		
		if ((this.eventPool.length > this.opts.POOL_SIZE) || (send_right_now))
		{
			this.prepareFeedTrace();
		}
	};


	/**
	 * @description prepare one block of stored traces for being sent
	 * @methodOf EventTracer
	 */
	this.prepareFeedTrace = function()
	{
		var currentLength = this.eventsToBeSend.length;

		var temp_array = new Array();

		for ( var i = 0 ; ((this.eventPool.length>0)&&(i < this.opts.POOL_SIZE )) ; i++ )
		{
			temp_array.push(this.eventPool.shift());
		}
		this.eventsToBeSend.push(temp_array);
		this.sendFeedTrace();
	};


	/**
	 * @description send one block of traces (non blocking)
	 * Does send the content of eventsToBeSend[0] to the server
	 * @methodOf EventTracer
	 */
	this.sendFeedTrace = function ()
	{
		var events = this.eventsToBeSend.pop();
		var sent_timeStamp = new Date().getTime();
		var params = $.extend({'events': events}, this.destinationService.params);
		
		$.ajax({
			url		: this.destinationService.url,
			data	: params,
			type	: this.destinationService.method,
			async	:true,
			datatype: this.destinationService.format,
			success : function(data, textStatus){ 
				_this.sendFeedTraceSucceed(data, textStatus, sent_timeStamp); 
			},
			error : function(xhr, errorString, exception){
				_this.sendFeedTraceFail(xhr, errorString, exception, events);
			}
		});
	};

	/**
	* @description success callback after traces sent. does affinate the size of traces package sent
	* @methodOf EventTracer
	* @param {String} data response from server
	* @param {String} textStatus status of request
	* @param {int} sent_timeStamp time the request was sent
	*/
	this.sendFeedTraceSucceed = function (data, textStatus, sent_timeStamp)//callback for sendfeedtrace
	{
		// adaptation of the send frequence
		var request_time = (new Date()).getTime() - sent_timeStamp;
		if (request_time > this.opts.time_limit_for_ajax_request)
		{
			// it takes too long
			this.increaseEventsPoolSize();
		}
		else
		{
			// we can increase the frequency of events storing
			this.reduceEventsPoolSize();
		}
		if (data.saved)
		{
			this.eventsToBeSend.shift();// data send, we can delete at 0 index
		}
	};

	/**
	 * @description the request took too much time, we increase the size of traces package, to have less frequent requests
	 * @methodOf EventTracer
	 */
	this.increaseEventsPoolSize = function ()
	{
		if ( this.opts.POOL_SIZE < this.opts.MAX_POOL_SIZE)
		{
			this.opts.POOL_SIZE = Math.floor(this.opts.POOL_SIZE * 2);
		}
	};

	/**
	 * @description the request was fast enough, we increase the frequency of requests by reducing the size of traces package
	 * @methodOf EventTracer
	 */
	this.reduceEventsPoolSize = function ()
	{
		if ( this.opts.POOL_SIZE > this.opts.MIN_POOL_SIZE )
		{
			this.opts.POOL_SIZE = Math.floor(this.opts.POOL_SIZE * 0.75);
		}
	};

	/**
	* @description callback function after request failed (TODO)
	* @methodOf EventTracer
	* @param {ressource} xhr ajax request ressource
	* @param {String} errorString error message
	* @param {exception} [exception] exception object thrown
	*/
	this.sendFeedTraceFail = function (xhr, errorString, exception, events)//callback for sendfeedtrace
	{
		this.increaseEventsPoolSize();
		
		this.eventsToBeSend.unshift(events);
		
		window.setInterval(this.sendAllFeedTrace_now, 2000);
	};


	/* no callback on success
	used when business events catched*/
	/**
	* @description send all traces with a blocking function
	* @methodOf EventTracer
	*/
	this.sendAllFeedTrace_now = function ()
	{
		var currentLength = this.eventsToBeSend.length;

		this.eventsToBeSend[ currentLength ] = Array();
		for (  ; this.eventPool.length > 0 ;  )//  empty the whole eventPool array
		{
			this.eventsToBeSend[ currentLength ].push( this.eventPool.pop() );
		}

		var events = new Array();
		for (var j in this.eventsToBeSend)
		{
			for (var i in this.eventsToBeSend[j])
			{
				events.push(this.eventsToBeSend[j][i]);
			}
		}

		var params = $.extend({'events': events }, this.destinationService.params);
		var sent_timeStamp = new Date().getTime();
		
		$.ajax({
			url		: this.destinationService.url,
			data	: params,
			type	: this.destinationService.method,
			async	: false,
			datatype: this.destinationService.format,
			success : function(data, textStatus){ 
				_this.sendFeedTraceSucceed(data, textStatus, sent_timeStamp); 
			},
			error : function(xhr, errorString, exception){
				_this.sendFeedTraceFail(xhr, errorString, exception, events);
			}
		});
	};
	
}

 
/**
 * @description bind every non bubbling events to dom elements.
 * @methodOf EventTracer
 */
jQuery.fn.bindDom = function(eventTracer)
{
	$(this).bind(eventTracer.EVENTS_TO_CATCH.nonBubbling.join(' ') , eventTracer.eventStation);
	var childrens = $(this).children();
	if (childrens.length)// stop condition
	{
		childrens.bindDom(eventTracer);
	}
};

/**
 * @description unbind platform events
 * @methodOf EventTracer
 */
jQuery.fn.unBindDom = function(eventTracer)
{
	
	$(this).unbind( eventTracer.EVENTS_TO_CATCH.nonBubbling.join(' ') , eventTracer.eventStation);
	var childrens = $(this).children();
	if (childrens.length)// stop condition
	{
		childrens.unBindDom(eventTracer);
	}
};

// attributes set in the pos tag
var ignored_pload_element_array = new Array('contentEditable','localName','tagname','textContent','namespaceURI','baseURI','innerHTML','defaultStatus','fullScreen','UNITSMAP','PROCESSURI','LANGID'
,'ITEMID','ACTIVITYID','DURATION','ELEMENT_NODE','ATTRIBUTE_NODE','TEXT_NODE','CDATA_SECTION_NODE','ENTITY_REFERENCE_NODE','ENTITY_NODE','PROCESSING_INSTRUCTION_NODE','COMMENT_NODE'
,'DOCUMENT_NODE','DOCUMENT_TYPE_NODE','DOCUMENT_FRAGMENT_NODE','NOTATION_NODE','DOCUMENT_POSITION_PRECEDING','DOCUMENT_POSITION_FOLLOWING','DOCUMENT_POSITION_CONTAINS','DOCUMENT_POSITION_CONTAINED_BY'
,'DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC','DOCUMENT_POSITION_DISCONNECTED','childElementCount','LAYOUT_DIRECTION','CURRENTSTIMULUS','CURRENTITEMEXTENSION','CURRENTSTIMULUSEXTENSION','nodeType','tabIndex');
var ignored_pload_event_array = new Array('cancelable','contentEditable','bubbles','tagName','localName','timeStamp','type');


/* custom events definition */

/* changeCss
*/
jQuery.event.special.changeCss = {setup:function(){},teardown:function(){}};
/* reloadMapEvent
order to reload the map */
jQuery.event.special.reloadMapEvent = {setup: function(){},teardown: function(){}};
