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
define([
    'jquery',
    'lodash',
    'i18n',
    'tpl!taoQtiTest/testRunner/tpl/navigator',
    'tpl!taoQtiTest/testRunner/tpl/navigatorTree',
    'util/capitalize'
], function ($, _, __, navigatorTpl, navigatorTreeTpl, capitalize) {
    'use strict';

    /**
     * List of CSS classes
     * @type {Object}
     * @private
     */
    var _cssCls = {
        active : 'active',
        collapsed : 'collapsed',
        collapsible : 'collapsible',
        masked : 'masked',
        disabled : 'disabled',
        flagged : 'flagged',
        answered : 'answered',
        viewed : 'viewed',
        unseen : 'unseen',
        icon : 'qti-navigator-icon',
        scope : {
            test : 'scope-test',
            testPart : 'scope-test-part',
            testSection : 'scope-test-section'
        }
    };

    /**
     * List of common CSS selectors
     * @type {Object}
     * @private
     */
    var _selectors = {
        component : '.qti-navigator',
        filterBar : '.qti-navigator-filters',
        tree : '.qti-navigator-tree',
        collapseHandle : '.qti-navigator-collapsible',
        linearState : '.qti-navigator-linear',
        infoAnswered : '.qti-navigator-answered .qti-navigator-counter',
        infoViewed : '.qti-navigator-viewed .qti-navigator-counter',
        infoUnanswered : '.qti-navigator-unanswered .qti-navigator-counter',
        infoFlagged : '.qti-navigator-flagged .qti-navigator-counter',
        infoPanel : '.qti-navigator-info',
        infoPanelLabels : '.qti-navigator-info > .qti-navigator-label',
        parts : '.qti-navigator-part',
        partLabels : '.qti-navigator-part > .qti-navigator-label',
        sections : '.qti-navigator-section',
        sectionLabels : '.qti-navigator-section > .qti-navigator-label',
        items : '.qti-navigator-item',
        itemLabels : '.qti-navigator-item > .qti-navigator-label',
        itemIcons : '.qti-navigator-item > .qti-navigator-icon',
        icons : '.qti-navigator-icon',
        linearStart : '.qti-navigator-linear-part button',
        counters : '.qti-navigator-counter',
        actives : '.active',
        collapsible : '.collapsible',
        collapsiblePanels : '.collapsible-panel',
        unseen : '.unseen',
        answered : '.answered',
        flagged : '.flagged',
        notFlagged : ':not(.flagged)',
        notAnswered : ':not(.answered)',
        masked : '.masked'
    };

    /**
     * Maps the filter mode to filter criteria.
     * Each filter criteria is a CSS selector used to find and mask the items to be discarded by the filter.
     * @type {Object}
     * @private
     */
    var _filterMap = {
        all : "",
        unanswered : _selectors.answered,
        flagged : _selectors.notFlagged,
        answered : _selectors.notAnswered,
        filtered : _selectors.masked
    };

    /**
     * Maps of config options translated from the context object to the local options
     * @type {Object}
     * @private
     */
    var _optionsMap = {
        'reviewScope' : 'reviewScope',
        'reviewPreventsUnseen' : 'preventsUnseen',
        'canCollapse' : 'canCollapse'
    };

    /**
     * Maps the handled review scopes
     * @type {Object}
     * @private
     */
    var _reviewScopes = {
        test : 'test',
        testPart : 'testPart',
        testSection : 'testSection'
    };

    /**
     * Provides a test review manager
     * @type {Object}
     */
    var testReview = {
        /**
         * Initializes the component
         * @param {String|jQuery|HTMLElement} element The element on which install the component
         * @param {Object} [options] A list of extra options
         * @param {String} [options.region] The region on which put the component: left or right
         * @param {String} [options.reviewScope] Limit the review screen to a particular scope:
         * the whole test, the current test part or the current test section)
         * @param {Boolean} [options.preventsUnseen] Prevents the test taker to access unseen items
         * @returns {testReview}
         */
        init: function init(element, options) {
            var initOptions = _.isObject(options) && options || {};
            var putOnRight = 'right' === initOptions.region;
            var insertMethod = putOnRight ? 'append' : 'prepend';

            this.options = initOptions;
            this.disabled = false;
            this.hidden = !!initOptions.hidden;
            this.currentFilter = 'all';

            // clean the DOM if the init method is called after initialisation
            if (this.$component) {
                this.$component.remove();
            }

            // build the component structure and inject it into the DOM
            this.$container = $(element);
            insertMethod = this.$container[insertMethod];
            if (insertMethod) {
                insertMethod.call(this.$container, navigatorTpl({
                    region: putOnRight ? 'right' : 'left',
                    hidden: this.hidden
                }));
            } else {
                throw new Error("Unable to inject the component structure into the DOM");
            }

            // install the component behaviour
            this._loadDOM();
            this._initEvents();
            this._updateDisplayOptions();

            return this;
        },

        /**
         * Links the component to the underlying DOM elements
         * @private
         */
        _loadDOM: function() {
            this.$component = this.$container.find(_selectors.component);

            // access to info panel displaying counters
            this.$infoAnswered = this.$component.find(_selectors.infoAnswered);
            this.$infoViewed = this.$component.find(_selectors.infoViewed);
            this.$infoUnanswered = this.$component.find(_selectors.infoUnanswered);
            this.$infoFlagged = this.$component.find(_selectors.infoFlagged);

            // access to filter switches
            this.$filterBar = this.$component.find(_selectors.filterBar);
            this.$filters = this.$filterBar.find('li');

            // access to the tree of parts/sections/items
            this.$tree = this.$component.find(_selectors.tree);

            // access to the panel displayed when a linear part is reached
            this.$linearState = this.$component.find(_selectors.linearState);
        },

        /**
         * Installs the event handlers on the underlying DOM elements
         * @private
         */
        _initEvents: function() {
            var self = this;

            // click on the collapse handle: collapse/expand the review panel
            this.$component.on('click' + _selectors.component, _selectors.collapseHandle, function() {
                if (self.disabled) {
                    return;
                }

                self.$component.toggleClass(_cssCls.collapsed);
                if (self.$component.hasClass(_cssCls.collapsed)) {
                    self._openSelected();
                }
            });

            // click on the info panel title: toggle the related panel
            this.$component.on('click' + _selectors.component, _selectors.infoPanelLabels, function() {
                if (self.disabled) {
                    return;
                }

                var $panel = $(this).closest(_selectors.infoPanel);
                self._togglePanel($panel, _selectors.infoPanel);
            });

            // click on a part title: toggle the related panel
            this.$tree.on('click' + _selectors.component, _selectors.partLabels, function() {
                if (self.disabled) {
                    return;
                }

                var $panel = $(this).closest(_selectors.parts);
                var open = self._togglePanel($panel, _selectors.parts);

                if (open) {
                    if ($panel.hasClass(_cssCls.active)) {
                        self._openSelected();
                    } else {
                        self._openOnly($panel.find(_selectors.sections).first(), $panel);
                    }
                }
            });

            // click on a section title: toggle the related panel
            this.$tree.on('click' + _selectors.component, _selectors.sectionLabels, function() {
                if (self.disabled) {
                    return;
                }

                var $panel = $(this).closest(_selectors.sections);

                self._togglePanel($panel, _selectors.sections);
            });

            // click on an item: jump to the position
            this.$tree.on('click' + _selectors.component, _selectors.itemLabels, function(event) {
                if (self.disabled) {
                    return;
                }

                var $item = $(this).closest(_selectors.items);
                var $target;

                if (!$item.hasClass(_cssCls.disabled)) {
                    $target = $(event.target);
                    if ($target.is(_selectors.icons) && !self.$component.hasClass(_cssCls.collapsed)) {
                        if (!$item.hasClass(_cssCls.unseen)) {
                            self._mark($item);
                        }
                    } else {
                        self._select($item);
                        self._jump($item);
                    }
                }
            });

            // click on the start button inside a linear part: jump to the position
            this.$tree.on('click' + _selectors.component, _selectors.linearStart, function() {
                if (self.disabled) {
                    return;
                }

                var $btn = $(this);

                if (!$btn.hasClass(_cssCls.disabled)) {
                    $btn.addClass(_cssCls.disabled);
                    self._jump($btn);
                }
            });

            // click on a filter button
            this.$filterBar.on('click' + _selectors.component, 'li', function() {
                if (self.disabled) {
                    return;
                }

                var $btn = $(this);
                var mode = $btn.data('mode');

                self.$filters.removeClass(_cssCls.active);
                self.$component.removeClass(_cssCls.collapsed);
                $btn.addClass(_cssCls.active);

                self._filter(mode);
            });
        },

        /**
         * Filters the items by a criteria
         * @param {String} criteria
         * @private
         */
        _filter: function(criteria) {
            var $items = this.$tree.find(_selectors.items).removeClass(_cssCls.masked);
            var filter = _filterMap[criteria];
            if (filter) {
                $items.filter(filter).addClass(_cssCls.masked);
            }
            this._updateSectionCounters(!!filter);
            this.currentFilter = criteria;
        },

        /**
         * Selects an item
         * @param {String|jQuery} position The item's position
         * @param {Boolean} [open] Forces the tree to be opened on the selected item
         * @returns {jQuery} Returns the selected item
         * @private
         */
        _select: function(position, open) {
            // find the item to select and extract its hierarchy
            var selected = position && position.jquery ? position : this.$tree.find('[data-position=' + position + ']');
            var hierarchy = selected.parentsUntil(this.$tree);

            // collapse the full tree and open only the hierarchy of the selected item
            if (open) {
                this._openOnly(hierarchy);
            }

            // select the item
            this.$tree.find(_selectors.actives).removeClass(_cssCls.active);
            hierarchy.add(selected).addClass(_cssCls.active);
            return selected;
        },

        /**
         * Opens the tree on the selected item only
         * @returns {jQuery} Returns the selected item
         * @private
         */
        _openSelected: function() {
            // find the selected item and extract its hierarchy
            var selected = this.$tree.find(_selectors.items + _selectors.actives);
            var hierarchy = selected.parentsUntil(this.$tree);

            // collapse the full tree and open only the hierarchy of the selected item
            this._openOnly(hierarchy);

            return selected;
        },

        /**
         * Collapses the full tree and opens only the provided branch
         * @param {jQuery} opened The element to be opened
         * @param {jQuery} [root] The root element from which collapse the panels
         * @private
         */
        _openOnly: function(opened, root) {
            (root || this.$tree).find(_selectors.collapsible).addClass(_cssCls.collapsed);
            opened.removeClass(_cssCls.collapsed);
        },

        /**
         * Toggles a panel
         * @param {jQuery} panel The panel to toggle
         * @param {String} [collapseSelector] Selector of panels to collapse
         * @returns {Boolean} Returns `true` if the panel just expanded now
         */
        _togglePanel: function(panel, collapseSelector) {
            var collapsed = panel.hasClass(_cssCls.collapsed);

            if (collapseSelector) {
                this.$tree.find(collapseSelector).addClass(_cssCls.collapsed);
            }

            if (collapsed) {
                panel.removeClass(_cssCls.collapsed);
            } else {
                panel.addClass(_cssCls.collapsed);
            }
            return collapsed;
        },

        /**
         * Sets the icon of a particular item
         * @param {jQuery} $item
         * @param {String} icon
         * @private
         */
        _setItemIcon: function($item, icon) {
            $item.find(_selectors.icons).attr('class', _cssCls.icon + ' icon-' + icon);
        },

        /**
         * Sets the icon of a particular item according to its state
         * @param {jQuery} $item
         * @private
         */
        _adjustItemIcon: function($item) {
            var icon = null;
            var defaultIcon = _cssCls.unseen;
            var iconCls = [
                _cssCls.flagged,
                _cssCls.answered,
                _cssCls.viewed
            ];

            _.forEach(iconCls, function(cls) {
                if ($item.hasClass(cls)) {
                    icon = cls;
                    return false;
                }
            });

            this._setItemIcon($item, icon || defaultIcon);
        },

        /**
         * Toggle the marked state of an item
         * @param {jQuery} $item
         * @param {Boolean} [flag]
         * @private
         */
        _toggleFlag: function($item, flag) {
            $item.toggleClass(_cssCls.flagged, flag);
            this._adjustItemIcon($item);
        },

        /**
         * Marks an item for later review
         * @param {jQuery} $item
         * @private
         */
        _mark: function($item) {
            var itemId = $item.data('id');
            var itemPosition = $item.data('position');
            var flag = !$item.hasClass(_cssCls.flagged);

            this._toggleFlag($item);

            /**
             * A storage of the flag is required
             * @event testReview#mark
             * @param {Boolean} flag - Tells whether the item is marked for review or not
             * @param {Number} position - The item position on which jump
             * @param {String} itemId - The identifier of the target item
             * @param {testReview} testReview - The client testReview component
             */
            this.trigger('mark', [flag, itemPosition, itemId]);
        },

        /**
         * Jumps to an item
         * @param {jQuery} $item
         * @private
         */
        _jump: function($item) {
            var itemId = $item.data('id');
            var itemPosition = $item.data('position');

            /**
             * A jump to a particular item is required
             * @event testReview#jump
             * @param {Number} position - The item position on which jump
             * @param {String} itemId - The identifier of the target item
             * @param {testReview} testReview - The client testReview component
             */
            this.trigger('jump', [itemPosition, itemId]);
        },

        /**
         * Updates the sections related items counters
         * @param {Boolean} filtered
         */
        _updateSectionCounters: function(filtered) {
            var self = this;
            var filter = _filterMap[filtered ? 'filtered' : 'answered'];
            this.$tree.find(_selectors.sections).each(function() {
                var $section = $(this);
                var $items = $section.find(_selectors.items);
                var $filtered = $items.filter(filter);
                var total = $items.length;
                var nb = total - $filtered.length;
                self._writeCount($section.find(_selectors.counters), nb, total);
            });
        },

        /**
         * Updates the display according to options
         * @private
         */
        _updateDisplayOptions: function() {
            var reviewScope = _reviewScopes[this.options.reviewScope] || 'test';
            var scopeClass = _cssCls.scope[reviewScope];
            var $root = this.$component;
            _.forEach(_cssCls.scope, function(cls) {
                $root.removeClass(cls);
            });
            if (scopeClass) {
                $root.addClass(scopeClass);
            }
            $root.toggleClass(_cssCls.collapsible, this.options.canCollapse);
        },

        /**
         * Updates the local options from the provided context
         * @param {Object} testContext The progression context
         * @private
         */
        _updateOptions: function(testContext) {
            var options = this.options;
            _.forEach(_optionsMap, function(optionKey, contextKey) {
                if (undefined !== testContext[contextKey]) {
                    options[optionKey] = testContext[contextKey];
                }
            });
        },

        /**
         * Updates the info panel
         */
        _updateInfos: function() {
            var progression = this.progression,
                unanswered = Number(progression.total) - Number(progression.answered);

            // update the info panel
            this._writeCount(this.$infoAnswered, progression.answered, progression.total);
            this._writeCount(this.$infoUnanswered, unanswered, progression.total);
            this._writeCount(this.$infoViewed, progression.viewed, progression.total);
            this._writeCount(this.$infoFlagged, progression.flagged, progression.total);
        },

        /**
         * Updates a counter
         * @param {jQuery} $place
         * @param {Number} count
         * @param {Number} total
         * @private
         */
        _writeCount: function($place, count, total) {
            $place.text(count + '/' + total);
        },

        /**
         * Gets the progression stats for the whole test
         * @param {Object} testContext The progression context
         * @returns {{total: (Number), answered: (Number), viewed: (Number), flagged: (Number)}}
         * @private
         */
        _getProgressionOfTest: function(testContext) {
            return {
                total : testContext.numberItems || 0,
                answered : testContext.numberCompleted || 0,
                viewed : testContext.numberPresented || 0,
                flagged : testContext.numberFlagged || 0
            };
        },

        /**
         * Gets the progression stats for the current test part
         * @param {Object} testContext The progression context
         * @returns {{total: (Number), answered: (Number), viewed: (Number), flagged: (Number)}}
         * @private
         */
        _getProgressionOfTestPart: function(testContext) {
            return {
                total : testContext.numberItemsPart || 0,
                answered : testContext.numberCompletedPart || 0,
                viewed : testContext.numberPresentedPart || 0,
                flagged : testContext.numberFlaggedPart || 0
            };
        },

        /**
         * Gets the progression stats for the current test section
         * @param {Object} testContext The progression context
         * @returns {{total: (Number), answered: (Number), viewed: (Number), flagged: (Number)}}
         * @private
         */
        _getProgressionOfTestSection: function(testContext) {
            return {
                total : testContext.numberItemsSection || 0,
                answered : testContext.numberCompletedSection || 0,
                viewed : testContext.numberPresentedSection || 0,
                flagged : testContext.numberFlaggedSection || 0
            };
        },

        /**
         * Updates the navigation tre
         * @param {Object} testContext The progression context
         */
        _updateTree: function(testContext) {
            var navigatorMap = testContext.navigatorMap;
            var reviewScope = this.options.reviewScope;
            var reviewScopePart = reviewScope === 'testPart';
            var reviewScopeSection = reviewScope === 'testSection';
            var _partsFilter = function(part) {
                if (reviewScopeSection && part.sections) {
                    part.sections = _.filter(part.sections, _partsFilter);
                }
                return part.active;
            };

            // rebuild the tree
            if (navigatorMap) {
                if (reviewScopePart || reviewScopeSection) {
                    // display only the current section
                    navigatorMap = _.filter(navigatorMap, _partsFilter);
                }

                this.$filterBar.show();
                this.$linearState.hide();
                this.$tree.html(navigatorTreeTpl({
                    parts: navigatorMap
                }));

                if (this.options.preventsUnseen) {
                    // disables all unseen items to prevent the test taker has access to.
                    this.$tree.find(_selectors.unseen).addClass(_cssCls.disabled);
                }
            } else {
                this.$filterBar.hide();
                this.$linearState.show();
                this.$tree.empty();
            }

            // apply again the current filter
            this._filter(this.$filters.filter(_selectors.actives).data('mode'));
        },

        /**
         * Set the marked state of an item
         * @param {Number|String|jQuery} position
         * @param {Boolean} flag
         */
        setItemFlag: function setItemFlag(position, flag) {
            var $item = position && position.jquery ? position : this.$tree.find('[data-position=' + position + ']');
            var progression = this.progression;

            // update the item flag
            this._toggleFlag($item, flag);

            // update the info panel
            progression.flagged = this.$tree.find(_selectors.flagged).length;
            this._writeCount(this.$infoFlagged, progression.flagged, progression.total);
            this._filter(this.currentFilter);
        },

        /**
         * Update the number of flagged items in the test context
         * @param {Object} testContext The test context
         * @param {Number} position The position of the flagged item
         * @param {Boolean} flag The flag state
         */
        updateNumberFlagged: function(testContext, position, flag) {
            var fields = ['numberFlagged'];
            var currentPosition = testContext.itemPosition;
            var currentFound = false, currentSection = null, currentPart = null;
            var itemFound = false, itemSection = null, itemPart = null;

            if (testContext.navigatorMap) {
                // find the current item and the marked item inside the navigator map
                // check if the marked item is in the current section
                _.forEach(testContext.navigatorMap, function(part) {
                    _.forEach(part && part.sections, function(section) {
                        _.forEach(section && section.items, function(item) {
                            if (item) {
                                if (item.position === position) {
                                    itemPart = part;
                                    itemSection = section;
                                    itemFound = true;
                                }
                                if (item.position === currentPosition) {
                                    currentPart = part;
                                    currentSection = section;
                                    currentFound = true;

                                }
                                if (itemFound && currentFound) {
                                    return false;
                                }
                            }
                        });

                        if (itemFound && currentFound) {
                            return false;
                        }
                    });

                    if (itemFound && currentFound) {
                        return false;
                    }
                });

                // select the context to update
                if (itemFound && currentPart === itemPart) {
                    fields.push('numberFlaggedPart');
                }
                if (itemFound && currentSection === itemSection) {
                    fields.push('numberFlaggedSection');
                }
            } else {
                // no navigator map, the current the marked item is in the current section
                fields.push('numberFlaggedPart');
                fields.push('numberFlaggedSection');
            }

            _.forEach(fields, function(field) {
                if (field in testContext) {
                    testContext[field] += flag ? 1 : -1;
                }
            });
        },

        /**
         * Get progression
         * @param {Object} testContext The progression context
         * @returns {object} progression
         */
        getProgression: function getProgression(testContext) {
            var reviewScope = _reviewScopes[this.options.reviewScope] || 'test',
                progressInfoMethod = '_getProgressionOf' + capitalize(reviewScope),
                getProgression = this[progressInfoMethod] || this._getProgressionOfTest,
                progression = getProgression && getProgression(testContext) || {};

            return progression;
        },

        /**
         * Updates the review screen
         * @param {Object} testContext The progression context
         * @returns {testReview}
         */
        update: function update(testContext) {
            this.progression = this.getProgression(testContext);
            this._updateOptions(testContext);
            this._updateInfos(testContext);
            this._updateTree(testContext);
            this._updateDisplayOptions(testContext);
            return this;
        },

        /**
         * Disables the component
         * @returns {testReview}
         */
        disable: function disable() {
            this.disabled = true;
            this.$component.addClass(_cssCls.disabled);
            return this;
        },

        /**
         * Enables the component
         * @returns {testReview}
         */
        enable: function enable() {
            this.disabled = false;
            this.$component.removeClass(_cssCls.disabled);
            return this;
        },

        /**
         * Hides the component
         * @returns {testReview}
         */
        hide: function hide() {
            this.disabled = true;
            this.hidden = true;
            this.$component.addClass(_cssCls.masked);
            return this;
        },

        /**
         * Shows the component
         * @returns {testReview}
         */
        show: function show() {
            this.disabled = false;
            this.hidden = false;
            this.$component.removeClass(_cssCls.masked);
            return this;
        },

        /**
         * Toggles the display state of the component
         * @param {Boolean} [show] External condition that's tells if the component must be shown or hidden
         * @returns {testReview}
         */
        toggle: function toggle(show) {
            if (undefined === show) {
                show = this.hidden;
            }

            if (show) {
                this.show();
            } else {
                this.hide();
            }

            return this;
        },

        /**
         * Install an event handler on the underlying DOM element
         * @param {String} eventName
         * @returns {testReview}
         */
        on: function on(eventName) {
            var dom = this.$component;
            if (dom) {
                dom.on.apply(dom, arguments);
            }

            return this;
        },

        /**
         * Uninstall an event handler from the underlying DOM element
         * @param {String} eventName
         * @returns {testReview}
         */
        off: function off(eventName) {
            var dom = this.$component;
            if (dom) {
                dom.off.apply(dom, arguments);
            }

            return this;
        },

        /**
         * Triggers an event on the underlying DOM element
         * @param {String} eventName
         * @param {Array|Object} extraParameters
         * @returns {testReview}
         */
        trigger : function trigger(eventName, extraParameters) {
            var dom = this.$component;

            if (undefined === extraParameters) {
                extraParameters = [];
            }
            if (!_.isArray(extraParameters)) {
                extraParameters = [extraParameters];
            }

            extraParameters.push(this);

            if (dom) {
                dom.trigger(eventName, extraParameters);
            }

            return this;
        }
    };

    /**
     * Builds an instance of testReview
     * @param {String|jQuery|HTMLElement} element The element on which install the component
     * @param {Object} [options] A list of extra options
     * @param {String} [options.region] The region on which put the component: left or right
     * @param {String} [options.reviewScope] Limit the review screen to a particular scope:
     * the whole test, the current test part or the current test section)
     * @param {Boolean} [options.preventsUnseen] Prevents the test taker to access unseen items
     * @returns {testReview}
     */
    var testReviewFactory = function(element, options) {
        var component = _.clone(testReview, true);
        return component.init(element, options);
    };

    return testReviewFactory;
});
