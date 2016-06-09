define([
    'IMSGlobal/jquery_2_1_1',
    'OAT/sts/common',
    'qtiInfoControlContext'
],
function ($, common, qtiInfoControlContext) {
    'use strict';

    /**
     * Create a PIC mock
     * @param {String} typeIdentifier
     * @param {Boolean} [preventInit]
     */
    var mockFactory = function(typeIdentifier, preventInit) {
        var picMock = {
            /**
             * The component identifier
             * @type {String|Number}
             */
            id: -1,

            /**
             * Gets the type of this component
             * @returns {string}
             */
            getTypeIdentifier: function () {
                return typeIdentifier;
            },

            /**
             * Initialize the PIC
             *
             * @param {String} id
             * @param {Node} dom
             * @param {Object} config - json
             */
            initialize: function (id, dom, config) {
                this.id = id;
                this.dom = dom;
                this.config = config || {};
                this.state = {};

                var $container = $(dom);

                if (!preventInit) {
                    common.init($container, this.config);
                }
            },

            /**
             * Reverse operation performed by render()
             * After this function is executed, only the initial naked markup remains
             * Event listeners are removed and the state and the response are reset
             */
            destroy: function () {
                $(this.dom).remove();
            },

            /**
             * Restore the state of the interaction from the serializedState.
             *
             * @param {Object} serializedState - json format
             */
            setSerializedState: function (serializedState) {
                this.state = serializedState;
            },

            /**
             * Get the current state of the interaction as a string.
             * It enables saving the state for later usage.
             * @returns {Object} json format
             */
            getSerializedState: function () {
                return this.state || {};
            }
        };

        qtiInfoControlContext.register(picMock);
    };

    /**
     * Build a PIC module
     * @param {String} moduleName
     * @param {String} [typeIdentifier]
     * @param {Boolean} [preventInit]
     */
    var moduleFactory = function(moduleName, typeIdentifier, preventInit) {
        define(moduleName, [], function() {
            mockFactory(typeIdentifier || moduleName, preventInit);
        });
    };

    return {
        pic : mockFactory,
        module : moduleFactory
    };
});
