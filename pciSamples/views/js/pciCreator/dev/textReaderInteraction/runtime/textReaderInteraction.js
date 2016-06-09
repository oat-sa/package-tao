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
 * Copyright (c) 2015 (original work) Open Assessment Technologies;
 *               
 */   
define(
    [
        'qtiCustomInteractionContext',
        'OAT/util/event',
        'textReaderInteraction/runtime/js/renderer'
    ],
    function (qtiCustomInteractionContext, event, Renderer) {
        'use strict';
        qtiCustomInteractionContext.register({
            id : -1,
            getTypeIdentifier : function () {
                return 'textReaderInteraction';
            },
            /**
             * Render the PCI : 
             * @param {String} id
             * @param {Node} dom
             * @param {Object} config - json
             */
            initialize : function (id, dom, config) {
                var that = this,
                    pci = this._taoCustomInteraction;

                this.id = id;
                this.dom = dom;
                this.config = config || {};
                this.$container = $(dom);
                
                //add method on(), off() and trigger() to the current object
                event.addEventMgr(this);

                if (!pci.widgetRenderer) {
                    pci.widgetRenderer = new Renderer({
                        serial : pci.serial,
                        $container : this.$container
                    });
                    pci.widgetRenderer.renderAll(pci.properties);
                }

                //page navigation events
                this.$container.on('click', '.js-prev-page, .js-next-page', function () {
                    var $button = $(this),
                        direction = $button.hasClass('js-next-page') ? 1 : -1,
                        currentPage = pci.widgetRenderer.tabsManager.index(),
                        index = currentPage + direction;

                    if (index >= 0 && pci.properties.pages.length > index) {
                        pci.widgetRenderer.tabsManager.index(index);
                    }
                });
                //tell the rendering engine that I am ready
                qtiCustomInteractionContext.notifyReady(this);
            },
            /**
             * Programmatically set the response following the json schema described in
             * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
             * 
             * @param {Object} interaction
             * @param {Object} response
             */
            setResponse : function (response) {
                //interaction has no response
            },
            /**
             * Get the response in the json format described in
             * http://www.imsglobal.org/assessment/pciv1p0cf/imsPCIv1p0cf.html#_Toc353965343
             * 
             * @param {Object} interaction
             * @returns {Object}
             */
            getResponse : function () {
                return {base : {boolean : true}};
            },
            /**
             * Remove the current response set in the interaction
             * The state may not be restored at this point.
             * 
             * @param {Object} interaction
             */
            resetResponse : function () {
                //interaction has no response
            },
            /**
             * Reverse operation performed by render()
             * After this function is executed, only the inital naked markup remains 
             * Event listeners are removed and the state and the response are reset
             * 
             * @param {Object} interaction
             */
            destroy : function () {
                this.$container.off().empty();
            },
            /**
             * Restore the state of the interaction from the serializedState.
             * 
             * @param {Object} interaction
             * @param {Object} serializedState - json format
             */
            setSerializedState : function (state) {
                
            },
            /**
             * Get the current state of the interaction as a string.
             * It enables saving the state for later usage.
             * 
             * @param {Object} interaction
             * @returns {Object} json format
             */
            getSerializedState : function () {
                return {};
            }
        });
    }
);