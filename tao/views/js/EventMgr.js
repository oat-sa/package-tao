/**
 * The EventMgr class enable you to manage event trought an high level layer.
 * It helps you to attach events and the associated callback to trig them.
 *
 * @require jquery >= 1.3.2 [http://jquery.com/]
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @author Jehan Bihin (class)
 *
 */

define(['require', 'jquery', 'class'], function(req, $) {
	var EventMgr = Class.extend({
		init: function() {
			this.eventTarget = $(document);
		},
		
		/**
		 *
		 * @param eventType
		 * @param callback
		 * @return
		 */
		bind: function(eventType, callback){
			return this.eventTarget.bind(eventType, callback);
		},
		
		/**
		 *
		 * @param eventType
		 * @param callback
		 * @return
		 */
		one: function(eventType, callback){
			return this.eventTarget.one(eventType, callback);
		},

		/**
		 *
		 * @param eventType
		 * @param params
		 * @return
		 */
		trigger: function(eventType, params){
			this.eventTarget.trigger(eventType, params);
		},

		/**
		 *
		 * @param eventType
		 * @param params
		 * @return
		 */
		unbind: function(eventType, params){
			return this.eventTarget.unbind(eventType);
		},

		unbindAll: function(params){
			return this.eventTarget.unbind();
		}
	});

	return EventMgr;
});