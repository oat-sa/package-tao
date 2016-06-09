define([
    'jquery',
    'lodash',
    'taoBackOffice/lib/vis/vis.min',
    'css!taoBackOffice/lib/vis/vis.min'
], function ($, _, vis) {
    'use strict';

    var network = null;
    var treeContainer = null;
    var settings = null;
    var data = null;

    /**
     * @private
     */
    function destroy() {
        if (network !== null) {
            network.destroy();
            network = null;
        }
    }

    var treeRender = {

        /**
         *
         * @param {Element} container
         * @param {Object} [treeData]
         * @param {Object} [options]
         */
        init: function (container, treeData, options) {

            if (!container instanceof Element) {
                throw new TypeError("tree container must be specified");
            }

            treeContainer = container;
            options = options || {};
            treeData = treeData || {nodes: [], edges: []};


            settings = {
                layout: {
                    hierarchical: {
                        sortMethod: 'directed',
                        "levelSeparation": 200
                    }
                },
                nodes: {
                    shape: 'box',
                    "color": {
                        "border": "#222",
                        "background": "#f2f0ee",

                        "highlight": {
                            "border": "#222",
                            "background": "#f2f0ee"
                        }
                    },

                    "font": {
                        "face": "'Franklin Gothic', 'Franklin Gothic Medium', 'Source Sans Pro', sans-serif",
                        "color": "#222"
                    }

                },
                edges: {
                    smooth: false,
                    width: 0.2,
                    color: {
                        color: "#222"
                    },
                    arrows: {to: true},
                    "physics": false,
                    "scaling": {
                        "label": false
                    }
                }
            };

            $.extend(settings, options);

            // create a network
            data = {
                nodes: treeData.nodes ? treeData.nodes : [],
                edges: treeData.edges ? treeData.edges : []
            };

        },

        run: function () {
            destroy();

            network = new vis.Network(treeContainer, data, settings);

            network.once('initRedraw', function () {

                if (data.nodes.length > 100) {
                    network.setOptions($.extend(settings, {
                        physics: {
                            hierarchicalRepulsion: {
                                nodeDistance: 200
                            },
                            stabilization: {
                                fit: false
                            }
                        }
                    }));

                    network.fit({
                        nodes: [data.nodes[0].id, data.nodes[1].id], animation: {
                            duration: 400,
                            easingFunction: 'linear'
                        }
                    });
                }

            });
        }
    };

    return treeRender;
});