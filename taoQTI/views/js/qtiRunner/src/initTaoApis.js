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
/**
 * TAO QTI initialize a QTI item in a <b>TAO Context</b>. 
 * It's a conveniance script to use it with the TAO platform.
 * On window load event, the recovery context is initialized and qti widget as well.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 * @requires jquery {@link http://www.jquery.com}
 */
var matchingParam 	= new Object();
var itemData  	= new Object();

var itemIdentifier = $("div.qti_item").attr('id') || '';
var qtiRunner = new QtiRunner();

function onItemApiReady(itemApi) {
	qtiRunner.setItemApi(itemApi);
	qtiRunner.setRenderer(new Qti.DefaultRenderer());
	qtiRunner.initItem(itemData);

	/*
	$.getJSON("events.json", function(json) {
		var eventTracer = new EventTracer();
		js
		eventTracer.initSourceService({ type: 'manual', data: JSON.parse(json)});
		eventTracer.initDestinationService(itemApi);
		itemApi.beforeFinish(function(){
			eventTracer.sendAllFeedTrace_now();
		});
	});
	*/
	$("#qti_validate").one('click',function(){qtiRunner.validate();});
};
