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
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([], function () {
    'use strict';

    var createEvent;
    var dispatchEvent;

    /**
     * Creates an event
     * @function createEvent
     * @param {String} eventName
     * @param {*} data
     */
    if (window.CustomEvent) {
        createEvent = function createEventUsingCustomEvent(eventName, data) {
            var event = new CustomEvent(eventName, {
                detail: data,
                bubbles: true,
                cancelable: true
            });
            return event;
        };
    } else if (document.createEvent) {
        createEvent = function createEventUsingCreateEvent(eventName, data) {
            var event = document.createEvent('Event');
            event.initEvent(eventName, true, true);
            event.detail = data;
            return event;
        };
    } else if (document.createEventObject) {
        createEvent = function createEventUsingCreateEventObject(eventName, data) {
            var event = document.createEventObject();
            event.detail = data;
            return event;
        };
    } else {
        createEvent = function createEventDummy() {
        };
    }

    /**
     * Dispatches an event
     * @function dispatchEvent
     * @param {HTMLElement} element
     * @param {String} eventName
     * @param {Event} event
     * @return {Boolean} Returns `true` if the event has been dispatched
     */
    if (document.dispatchEvent) {
        dispatchEvent = function dispatchEventUsingDispatchEvent(element, eventName, event) {
            if (element) {
                element.dispatchEvent(event);
                return true;
            }
            return false;
        };
    } else if (document.fireEvent) {
        dispatchEvent = function dispatchEventUsingFireEvent(element, eventName, event) {
            if (element) {
                element.fireEvent('on' + eventName, event);
                return true;
            }
            return false;
        };
    } else {
        dispatchEvent = function dispatchEventDummy() {
            return false;
        };
    }


    /**
     * Triggers a custom event using native methods
     * @param {HTMLElement} element
     * @param {String} eventName
     * @param {*} data
     * @returns {Boolean} Returns true if the event has been successfully triggered
     */
    var triggerCustomEvent = function triggerCustomEvent(element, eventName, data) {
        var event = createEvent(eventName, data);
        return dispatchEvent(element, eventName, event);
    };

    return triggerCustomEvent;
});
