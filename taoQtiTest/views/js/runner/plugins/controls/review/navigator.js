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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
define([
    'jquery',
    'lodash',
    'i18n',
    'ui/component',
    'taoQtiTest/runner/helpers/map',
    'tpl!taoQtiTest/runner/plugins/controls/review/navigator',
    'tpl!taoQtiTest/runner/plugins/controls/review/navigatorTree'
], function ($, _, __, component, mapHelper, navigatorTpl, navigatorTreeTpl) {
    'use strict';

    /**
     * Some default values
     * @type {Object}
     * @private
     */
    var _defaults = {
        scope: 'test',
        canCollapse: false,
        preventsUnseen: true,
        hidden: false
    };

    /**
     * List of CSS classes
     * @type {Object}
     * @private
     */
    var _cssCls = {
        active: 'active',
        collapsed: 'collapsed',
        collapsible: 'collapsible',
        masked: 'masked',
        disabled: 'disabled',
        flagged: 'flagged',
        answered: 'answered',
        viewed: 'viewed',
        unseen: 'unseen',
        icon: 'qti-navigator-icon',
        scope: {
            test: 'scope-test',
            testPart: 'scope-test-part',
            testSection: 'scope-test-section'
        }
    };

    /**
     * List of icon CSS classes
     * @type {Array}
     * @private
     */
    var _iconCls = [
        _cssCls.flagged,
        _cssCls.answered,
        _cssCls.viewed
    ];

    /**
     * List of common CSS selectors
     * @type {Object}
     * @private
     */
    var _selectors = {
        component: '.qti-navigator',
        filterBar: '.qti-navigator-filters',
        tree: '.qti-navigator-tree',
        collapseHandle: '.qti-navigator-collapsible',
        linearState: '.qti-navigator-linear',
        infoAnswered: '.qti-navigator-answered .qti-navigator-counter',
        infoViewed: '.qti-navigator-viewed .qti-navigator-counter',
        infoUnanswered: '.qti-navigator-unanswered .qti-navigator-counter',
        infoFlagged: '.qti-navigator-flagged .qti-navigator-counter',
        infoPanel: '.qti-navigator-info',
        infoPanelLabels: '.qti-navigator-info > .qti-navigator-label',
        parts: '.qti-navigator-part',
        partLabels: '.qti-navigator-part > .qti-navigator-label',
        sections: '.qti-navigator-section',
        sectionLabels: '.qti-navigator-section > .qti-navigator-label',
        items: '.qti-navigator-item',
        itemLabels: '.qti-navigator-item > .qti-navigator-label',
        itemIcons: '.qti-navigator-item > .qti-navigator-icon',
        icons: '.qti-navigator-icon',
        linearStart: '.qti-navigator-linear-part button',
        counters: '.qti-navigator-counter',
        actives: '.active',
        collapsible: '.collapsible',
        collapsiblePanels: '.collapsible-panel',
        unseen: '.unseen',
        answered: '.answered',
        flagged: '.flagged',
        notFlagged: ':not(.flagged)',
        notAnswered: ':not(.answered)',
        masked: '.masked'
    };

    /**
     * Maps the filter mode to filter criteria.
     * Each filter criteria is a CSS selector used to find and mask the items to be discarded by the filter.
     * @type {Object}
     * @private
     */
    var _filterMap = {
        all: "",
        unanswered: _selectors.answered,
        flagged: _selectors.notFlagged,
        answered: _selectors.notAnswered,
        filtered: _selectors.masked
    };

    /**
     *
     * @type {Object}
     */
    var navigatorApi = {
        /**
         * Updates the stats on the flagged items in the current map
         * @param {Number} position
         * @param {Boolean} flag
         */
        updateStats: function updateStats(position, flag) {
            var map = this.map;
            var item;

            if (map) {
                item = mapHelper.getItemAt(map, position);

                if (item) {
                    item.flagged = flag;
                    mapHelper.updateItemStats(map, position);
                }
            }
        },

        /**
         * Set the marked state of an item
         * @param {Number|String|jQuery} position
         * @param {Boolean} flag
         */
        setItemFlag: function setItemFlag(position, flag) {
            var $item = position && position.jquery ? position : this.controls.$tree.find('[data-position=' + position + ']');
            var progression = this.progression;
            var icon;

            // update the map stats
            this.updateStats(position, flag);

            // update the item flag
            $item.toggleClass(_cssCls.flagged, flag);

            // set the item icon according to its state
            icon = _.find(_iconCls, _.bind($item.hasClass, $item)) || _cssCls.unseen;
            $item.find(_selectors.icons).attr('class', _cssCls.icon + ' icon-' + icon);

            // update the info panel
            progression.flagged = this.controls.$tree.find(_selectors.flagged).length;
            this.writeCount(this.controls.$infoFlagged, progression.flagged, progression.total);

            // recompute the filters
            this.filter(this.currentFilter);
        },

        /**
         * Filters the items by a criteria
         * @param {String} criteria
         */
        filter: function filter(criteria) {
            var self = this;

            // remove the current filter by restoring all items
            var $items = this.controls.$tree.find(_selectors.items).removeClass(_cssCls.masked);

            // filter the items according to the provided criteria
            var filter = _filterMap[criteria];
            var filtered = _filterMap[filter ? 'filtered' : 'answered'];
            if (filter) {
                $items.filter(filter).addClass(_cssCls.masked);
            }

            // update the section counters
            this.controls.$tree.find(_selectors.sections).each(function () {
                var $section = $(this);
                var $items = $section.find(_selectors.items);
                var $filtered = $items.filter(filtered);
                var total = $items.length;
                var nb = total - $filtered.length;
                self.writeCount($section.find(_selectors.counters), nb, total);
            });
            this.currentFilter = criteria;
        },

        /**
         * Update the config
         * @param {Object} [config]
         * @returns {navigatorApi}
         */
        updateConfig: function updateConfig(config) {
            var $component = this.getElement();
            var scopeClass = _cssCls.scope[this.config.scope || _defaults.scope];

            // apply the new config
            config = _.merge(this.config, config || {});

            // enable/disable the collapsing of the panel
            $component.toggleClass(_cssCls.collapsible, config.canCollapse);

            // update the component CSS class according to the scope
            $component.removeClass(scopeClass);
            scopeClass = _cssCls.scope[this.config.scope || _defaults.scope];
            $component.addClass(scopeClass);

            // update component visibility
            if (config.hidden) {
                this.hide();
            } else {
                this.show();
            }

            return this;
        },

        /**
         * Updates the review screen
         * @param {Object} map The current test map
         * @param {Object} context The current test context
         * @returns {navigatorApi}
         */
        update: function update(map, context) {
            var scopedMap = this.getScopedMap(map, context);
            var progression = scopedMap.stats || {
                    answered: 0,
                    flagged: 0,
                    viewed: 0,
                    total: 0
                };

            this.map = map;
            this.progression = progression;

            // update the info panel
            this.writeCount(this.controls.$infoAnswered, progression.answered, progression.total);
            this.writeCount(this.controls.$infoUnanswered, progression.total - progression.answered, progression.total);
            this.writeCount(this.controls.$infoViewed, progression.viewed, progression.total);
            this.writeCount(this.controls.$infoFlagged, progression.flagged, progression.total);

            // rebuild the tree
            if (!context.isLinear) {
                this.controls.$filterBar.show();
                this.controls.$linearState.hide();
                this.controls.$tree.html(navigatorTreeTpl(scopedMap));

                if (this.config.preventsUnseen) {
                    // disables all unseen items to prevent the test taker has access to.
                    this.controls.$tree.find(_selectors.unseen).addClass(_cssCls.disabled);
                }
            } else {
                this.controls.$filterBar.hide();
                this.controls.$linearState.show();
                this.controls.$tree.empty();
            }

            // apply again the current filter
            this.filter(this.controls.$filters.filter(_selectors.actives).data('mode'));

            return this;
        },

        /**
         * Gets the scoped map
         * @param {Object} map The current test map
         * @param {Object} context The current test context
         * @returns {object} The scoped map
         */
        getScopedMap: function getScopedMap(map, context) {
            // need a clone of the map as we will change some properties
            var scopedMap = _.cloneDeep(map || {});

            // gets the current part/section/item
            var testPart = scopedMap.parts[context.testPartId] || {};
            var section = testPart.sections && testPart.sections[context.sectionId] || {};
            var item = section.items && section.items[context.itemIdentifier] || {};

            // set the active part/section/item
            testPart.active = true;
            section.active = true;
            item.active = true;

            // select the scoped map fragment
            switch (this.config.scope) {
                case 'testSection':
                    // build a scoped map containing only the current part, the current section and its items
                    scopedMap = {
                        parts: {},
                        stats: section.stats
                    };
                    testPart.sections = {};
                    testPart.sections[context.sectionId] = section;
                    scopedMap.parts[context.testPartId] = testPart;
                    break;

                case 'testPart':
                    // build a scoped map containing only the current part and its sections
                    scopedMap = {
                        parts: {},
                        stats: testPart.stats
                    };
                    scopedMap.parts[context.testPartId] = testPart;
                    break;

                default:
                case 'test':
                    // keep the whole map
                    break;
            }

            return scopedMap;
        },

        /**
         * Updates a counter
         * @param {jQuery} $place
         * @param {Number} count
         * @param {Number} total
         * @private
         */
        writeCount: function writeCount($place, count, total) {
            $place.text(count + '/' + total);
        },

        /**
         * Selects an item
         * @param {String|jQuery} position The item's position
         * @param {Boolean} [open] Forces the tree to be opened on the selected item
         * @returns {jQuery} Returns the selected item
         */
        select: function select(position, open) {
            // find the item to select and extract its hierarchy
            var $tree = this.controls.$tree;
            var selected = position && position.jquery ? position : $tree.find('[data-position=' + position + ']');
            var hierarchy = selected.parentsUntil($tree);

            // collapse the full tree and open only the hierarchy of the selected item
            if (open) {
                this.openOnly(hierarchy);
            }

            // select the item
            $tree.find(_selectors.actives).removeClass(_cssCls.active);
            hierarchy.add(selected).addClass(_cssCls.active);
            return selected;
        },

        /**
         * Opens the tree on the selected item only
         * @returns {jQuery} Returns the selected item
         */
        openSelected: function openSelected() {
            // find the selected item and extract its hierarchy
            var $tree = this.controls.$tree;
            var selected = $tree.find(_selectors.items + _selectors.actives);
            var hierarchy = selected.parentsUntil($tree);

            // collapse the full tree and open only the hierarchy of the selected item
            this.openOnly(hierarchy);

            return selected;
        },

        /**
         * Collapses the full tree and opens only the provided branch
         * @param {jQuery} opened The element to be opened
         * @param {jQuery} [root] The root element from which collapse the panels
         */
        openOnly: function openOnly(opened, root) {
            (root || this.controls.$tree).find(_selectors.collapsible).addClass(_cssCls.collapsed);
            opened.removeClass(_cssCls.collapsed);
        },

        /**
         * Toggles a panel
         * @param {jQuery} panel The panel to toggle
         * @param {String} [collapseSelector] Selector of panels to collapse
         * @returns {Boolean} Returns `true` if the panel just expanded now
         */
        togglePanel: function togglePanel(panel, collapseSelector) {
            var collapsed = panel.hasClass(_cssCls.collapsed);

            if (collapseSelector) {
                this.controls.$tree.find(collapseSelector).addClass(_cssCls.collapsed);
            }

            if (collapsed) {
                panel.removeClass(_cssCls.collapsed);
            } else {
                panel.addClass(_cssCls.collapsed);
            }
            return collapsed;
        },

        /**
         * Toggles the display state of the component
         * @param {Boolean} [show] External condition that's tells if the component must be shown or hidden
         * @returns {navigatorApi}
         */
        toggle: function toggle(show) {
            if (undefined === show) {
                show = this.is('hidden');
            }

            if (show) {
                this.show();
            } else {
                this.hide();
            }

            return this;
        }
    };

    /**
     *
     * @param {Object} config
     * @param {String} [config.scope] Limit the review screen to a particular scope: test, testPart, testSection
     * @param {Boolean} [config.preventsUnseen] Prevents the test taker to access unseen items
     * @param {Boolean} [config.canCollapse] Allow the test taker to collapse the component
     * @param {Boolean} [config.canFlag] Allow the test taker to flag items
     * @param {Boolean} [config.hidden] Hide the component at init
     * @param {Object} map The current test map
     * @param {Object} context The current test context
     * @returns {*}
     */
    function navigatorFactory(config, map, context) {
        /**
         * Flags an item
         * @param {jQuery} $item
         */
        function flagItem($item) {
            var position = $item.data('position');
            var flagged = !$item.hasClass(_cssCls.flagged);

            // update the display
            navigator.setItemFlag(position, flagged);

            /**
             * An item is flagged
             * @event navigator#flag
             * @param {Number} position - The item position on which jump
             * @param {Boolean} flag - Tells whether the item is marked for review or not
             */
            navigator.trigger('flag', position, flagged);
        }

        /**
         * Jumps to an item
         * @param {jQuery} $item
         * @private
         */
        function jump($item) {
            var position = $item.data('position');

            /**
             * A jump to a particular item is required
             * @event navigator#jump
             * @param {Number} position - The item position on which jump
             */
            navigator.trigger('jump', position);
        }

        var navigator = component(navigatorApi, _defaults)
            .setTemplate(navigatorTpl)

            // post init
            .on('init', function () {
                if (!this.is('rendered')) {
                    this.render();
                }

                this.update(map, context);
            })

            // uninstalls the component
            .on('destroy', function () {
                this.controls = null;
            })

            // renders the component
            .on('render', function () {
                var self = this;

                // main component elements
                var $component = this.getElement();
                var $filterBar = $component.find(_selectors.filterBar);
                var $filters = $filterBar.find('li');
                var $tree = $component.find(_selectors.tree);

                // links the component to the underlying DOM elements
                this.controls = {
                    // access to info panel displaying counters
                    $infoAnswered: $component.find(_selectors.infoAnswered),
                    $infoViewed: $component.find(_selectors.infoViewed),
                    $infoUnanswered: $component.find(_selectors.infoUnanswered),
                    $infoFlagged: $component.find(_selectors.infoFlagged),

                    // access to filter switches
                    $filterBar: $filterBar,
                    $filters: $filters,

                    // access to the tree of parts/sections/items
                    $tree: $tree,

                    // access to the panel displayed when a linear part is reached
                    $linearState: $component.find(_selectors.linearState)
                };

                // apply options
                this.updateConfig();

                // click on the collapse handle: collapse/expand the review panel
                $component.on('click' + _selectors.component, _selectors.collapseHandle, function () {
                    if (!self.is('disabled')) {
                        $component.toggleClass(_cssCls.collapsed);
                        if ($component.hasClass(_cssCls.collapsed)) {
                            self.openSelected();
                        }
                    }
                });

                // click on the info panel title: toggle the related panel
                $component.on('click' + _selectors.component, _selectors.infoPanelLabels, function () {
                    if (!self.is('disabled')) {
                        self.togglePanel($(this).closest(_selectors.infoPanel), _selectors.infoPanel);
                    }
                });

                // click on a part title: toggle the related panel
                $tree.on('click' + _selectors.component, _selectors.partLabels, function () {
                    var $panel;

                    if (!self.is('disabled')) {
                        $panel = $(this).closest(_selectors.parts);

                        if (self.togglePanel($panel, _selectors.parts)) {
                            if ($panel.hasClass(_cssCls.active)) {
                                self.openSelected();
                            } else {
                                self.openOnly($panel.find(_selectors.sections).first(), $panel);
                            }
                        }
                    }

                });

                // click on a section title: toggle the related panel
                $tree.on('click' + _selectors.component, _selectors.sectionLabels, function () {
                    if (!self.is('disabled')) {
                        self.togglePanel($(this).closest(_selectors.sections), _selectors.sections);
                    }
                });

                // click on an item: jump to the position
                $tree.on('click' + _selectors.component, _selectors.itemLabels, function (event) {
                    var $item, $target;

                    if (!self.is('disabled')) {
                        $item = $(this).closest(_selectors.items);

                        if (!$item.hasClass(_cssCls.disabled)) {
                            $target = $(event.target);
                            if (self.config.canFlag && $target.is(_selectors.icons) && !$component.hasClass(_cssCls.collapsed)) {
                                // click on the icon, just flag the item, unless the panel is collapsed
                                if (!$item.hasClass(_cssCls.unseen)) {
                                    flagItem($item);
                                }
                            } else {
                                // go to the selected item
                                self.select($item);
                                jump($item);
                            }
                        }
                    }
                });

                // click on the start button inside a linear part: jump to the position
                $tree.on('click' + _selectors.component, _selectors.linearStart, function () {
                    var $btn;

                    if (!self.is('disabled')) {
                        $btn = $(this);

                        // go to the first item of the linear part
                        if (!$btn.hasClass(_cssCls.disabled)) {
                            $btn.addClass(_cssCls.disabled);
                            jump($btn);
                        }
                    }

                });

                // click on a filter button
                $filterBar.on('click' + _selectors.component, 'li', function () {
                    var $btn, mode;

                    if (!self.is('disabled')) {
                        $btn = $(this);
                        mode = $btn.data('mode');

                        // select the button
                        $filters.removeClass(_cssCls.active);
                        $component.removeClass(_cssCls.collapsed);
                        $btn.addClass(_cssCls.active);

                        // filter the items
                        self.filter(mode);
                    }
                });
            });

        // set default filter
        navigator.currentFilter = 'all';

        // the component will be ready
        return navigator.init(config);
    }

    return navigatorFactory;
});
