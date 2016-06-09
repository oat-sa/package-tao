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
 *
 * @author dieter <dieter@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/feedback',
    'ui/modal',
    'tpl!taoDelivery/tpl/fullscreen-modal-feedback'
], function ($, _, __, feedback, modal, dialogTpl) {
    'use strict';

    var $dialog;
    var $body;
    var d = document;
    var dElem = d.documentElement;

    var fs = {
        changeInterval: null,

        isSupported: !!(d.exitFullscreen ||
                        d.msExitFullscreen ||
                        d.mozCancelFullScreen ||
                        d.webkitExitFullscreen),

        requestFullscreen: dElem.requestFullscreen ||
                    dElem.msRequestFullscreen ||
                    dElem.mozRequestFullScreen ||
                    function() {
                        dElem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    },

                fullscreenchange: (function() {
                    var prefixes = ['', 'ms', 'moz', 'webkit'],
                        i = prefixes.length;
                    while(i--) {
                        if('on' + prefixes[i] + 'fullscreenchange' in dElem) {
                            return prefixes[i] + 'fullscreenchange';
                        }
                    }
                    return 'myfullscreenchange';
                }()),

        fullScreen: function() {
            return !!(d.fullscreenElement || d.mozFullScreen || d.webkitIsFullScreen ||
                        (screen.availHeight || screen.height - 30) <= window.innerHeight);
        },

        // on older browsers wait for a full screen change to happen
        // and fire the change event manually
        awaitFsChange : function() {
            var event = d.createEvent('Event');
            event.initEvent(fs.fullscreenchange, true, true);
            clearInterval(fs.changeInterval);
            fs.changeInterval = setInterval(function() {
                if(!fs.fullScreen()) {
                    d.dispatchEvent(event);
                }
            }, 2000);
        }
    };

    /**
     * React to user input on the prompt which is either
     * key press or click on the button
     *
     * @param evt
     */
    var handleUserInput = function handleUserInput (evt) {

        // full screen needs to be initiated by pressing
        // F11 (Windows/Linux) or Ctrl+Cmd+F (Mac)
        if(!fs.isSupported) {
            $dialog.modal('close');
            return;
        }

        // accept 'enter' as only valid key stroke
        if(evt.type === 'keydown' && (evt.keyCode || evt.which) !== 13) {
            return;
        }

        // in all other cases either 'enter' has been hit or the enter button has been clicked
        fs.requestFullscreen.call(dElem);
        dElem.className += ' fullscreen';
        $dialog.modal('close');
    };

    /**
     * Triggers a resize
     */
    var triggerResize = (function() {
        return _.throttle(function() {
            var frame = document.getElementById('iframeDeliveryExec');
            var frameWindow = frame && frame.contentWindow;
            var frame$ = frameWindow && frameWindow.$;
            var $win = frame$ && frame$(frameWindow) || $(window);
            $win.trigger('resize');
        }, 250);
    })();

    /**
     * Initialize full screen
     */
    var init = function init() {

        $body = $(document.body);

        // listen either to the native or the change event created in the observer above
        document.addEventListener(fs.fullscreenchange, function() {
            if(!fs.fullScreen()) {
                dElem.className = dElem.className.replace(/\bfullscreen\b/, '');
                $dialog.modal('open');
            } else {
                triggerResize();
            }
        });
        if (!fs.isSupported) {
            fs.awaitFsChange();
        }

        modal($body);
        $dialog = $(dialogTpl({
            fsSupported: fs.isSupported,
            // while this is vague chances that any Mac browser gets here are very little
            launchButton: navigator.platform.toLowerCase().indexOf('Mac') === 0 ? 'Ctrl+⌘+F' : 'F11'
        }));

        $dialog[0].querySelector('button').addEventListener('click', function(e) {
            handleUserInput(e);
        });

        document.addEventListener('keydown', function(e) {
            if(!fs.fullScreen()) {
                handleUserInput(e);
            }
        });

        $dialog.on('opened.modal', function() {
            clearInterval(fs.changeInterval);
        });

        $dialog.on('closed.modal', function() {
            if (!fs.isSupported) {
                fs.awaitFsChange();
                triggerResize();
            }
        });

        $body.append($dialog);
        
        $dialog.modal({
            width: 500,
            animate: false,
            disableClosing: true,
            startClosed: true
        });

        // Note that when a page is on full screen already on load (after F5 normally)
        // fullscreenElement and therefor fs.fullScreen() will report the wrong value!
        if(false === ((screen.availHeight || screen.height - 30) <= window.innerHeight)) {
            $dialog.modal('open');
        }
    };

    /**
     * @exports
     */
    return {
        init: init
    };
});
