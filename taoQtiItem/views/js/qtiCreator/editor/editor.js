define([
    'lodash',
    'jquery',
    'i18n',
    'tpl!taoQtiItem/qtiCreator/tpl/toolbars/tooltip',
    'ui/tooltipster'
], function (_, $, __, tooltipTpl, tooltip) {

    'use strict';

    var editor = (function () {

        var elements = {},
            $win = $(window),
            $doc = $(document);

        /**
         * Handle item scrolling
         *
         * @private
         */
        var _handleScrolling = function ($itemContainer) {

            var sidePadding = parseInt(elements.scrollInner.css('padding-left')) * 2,
                requiredWidth = $itemContainer.outerWidth() + sidePadding,
                availableWidth = elements.scrollInner.innerWidth(),
                areaHeight = $(window).height() - elements.itemPanel.offset().top + $win.scrollTop(),
                actualWidth = Math.max(requiredWidth, availableWidth);

            // max-height = 'none' on first run, here set to the height calculated by _adaptHeight()
            if(isNaN(parseInt(elements.scrollOuter.css('max-height')))) {
                elements.scrollOuter.css('max-height', elements.itemPanel.css('height'));
                elements.itemPanel.css('height', '');
            }

            if(requiredWidth > availableWidth) {
                elements.scrollInner[0].style.width = actualWidth + 'px';
            }
            else {
                elements.scrollInner.width('');
            }
            elements.scrollOuter.height(areaHeight);
        };


        var _setupElements = function () {
            var _elements = {
                    scope: '#item-editor-scope',
                    toolbar: '#item-editor-toolbar',
                    toolbarInner: '#item-editor-toolbar-inner',
                    sidebars: '.item-editor-sidebar',
                    itemBar: '#item-editor-item-bar',
                    itemPanel: '#item-editor-panel',
                    scrollOuter: '#item-editor-scroll-outer',
                    scrollInner: '#item-editor-scroll-inner'
                },
                element;
            for (element in _elements) {
                elements[element] = $(_elements[element]);
            }
            elements.columns = elements.sidebars.add(elements.itemPanel);
        };

        // selectors and classes
        var heading = 'h2',
            section = 'section',
            panel = 'hr, .panel',
            closed = 'closed',
            ns = 'accordion';


        var buildSubGroups = function () {

            elements.sidebars.find('[data-sub-group]').each(function () {
                var $element = $(this),
                    $section = $element.parents('section'),
                    subGroup = $element.data('sub-group'),
                    $subGroupPanel,
                    $subGroupList,
                    $cover;

                if (!subGroup) {
                    return;
                }

                $subGroupPanel = $section.find('.sub-group.' + subGroup);
                $subGroupList = $subGroupPanel.find('.tool-list');
                if (!$subGroupPanel.length) {
                    $subGroupPanel = $('<div>', { 'class': 'panel clearfix sub-group ' + subGroup });
                    $subGroupList = $('<ul>', { 'class': 'tool-list plain clearfix' });
                    $subGroupPanel.append($subGroupList);
                    $section.append($subGroupPanel);
                    $cover = $('<div>', { 'class': 'sub-group-cover blocking'});
                    $subGroupPanel.append($cover);
                    $subGroupPanel.data('cover', $cover);
                }
                $subGroupList.append($element);
            });

            addInlineInteractionTooltip();
        };

        /**
         * setup accordion
         */
        var sidebarAccordionInit = function () {

            elements.sidebars.each(function () {
                var $sidebar = $(this),
                    $sections = $sidebar.find(section),
                    $allPanels = $sidebar.children(panel).hide(),
                    $allTriggers = $sidebar.find(heading);

                if ($allTriggers.length === 0) {
                    return true;
                }


                // setup events
                $allTriggers.each(function () {
                    var $heading = $(this),
                        $section = $heading.parents(section),
                        $panel = $section.children(panel),
                        $closer = $('<span>', { 'class': 'icon-up'}),
                        $opener = $('<span>', { 'class': 'icon-down'}),
                        action = $panel.is(':visible') ? 'open' : 'close';

                    $heading.append($closer).append($opener).addClass(closed);

                    // toggle heading class arrow (actually switch arrow)
                    $panel.on('panelclose.' + ns + ' panelopen.' + ns, function (e, args) {
                        var fn = e.type === 'panelclose' ? 'add' : 'remove';
                        args.heading[fn + 'Class'](closed);
                    });


                    $panel.trigger('panel' + action + '.' + ns, { heading: $heading });
                });


                $sections.each(function () {

                    // assign click action to headings
                    $(this).find(heading).on('click', function (e, args) {

                        var $heading = $(this),
                            $panel = $heading.parents(section).children(panel),
                            preserveOthers = !!(args && args.preserveOthers),
                            actions = {
                                close: 'hide',
                                open: 'fadeIn'
                            },
                            action,
                            forceState = (args && args.forceState ? args.forceState : false),
                            classFn;

                        if (forceState) {
                            classFn = forceState === 'open' ? 'addClass' : 'removeClass';
                            $heading[classFn](closed);
                        }

                        action = $heading.hasClass(closed) ? 'open' : 'close';

                        // whether or not to close other sections in the same sidebar
                        // @todo (optional): remove 'false' in the condition below
                        // to change the style to accordion, i.e. to allow for only one open section
                        if (false && !preserveOthers) {
                            $allPanels.not($panel).each(function () {
                                var $panel = $(this),
                                    $heading = $panel.parent().find(heading),
                                    _action = 'close';

                                $panel.trigger('panel' + _action + '.' + ns, { heading: $heading })[actions[_action]]();
                            });
                        }

                        $panel.trigger('panel' + action + '.' + ns, { heading: $heading })[actions[action]]();
                    });

                });
            });
        };

        /**
         * Toggle section display
         *
         * @param sections
         */
        var _toggleSections = function (sections, preserveOthers, state) {
            sections.each(function () {
                $(this).find(heading).trigger('click', { preserveOthers: preserveOthers, forceState: state });
            });
        };

        /**
         * Close specific sections
         *
         * @param sections
         */
        var closeSections = function (sections, preserveOthers) {
            _toggleSections(sections, !!preserveOthers, 'close');
        };

        /**
         * Open specific sections
         *
         * @param sections
         */
        var openSections = function (sections, preserveOthers) {
            _toggleSections(sections, !!preserveOthers, 'open');
        };

        /**
         * Adapt height of sidebars and content
         */
        var adaptHeight = function () {
            var height = 0;
            elements.columns.each(function () {
                var block = $(this);
                height = Math.max(block.height(), height);
            }).height(height);
        };


        /**
         * toggle availability of sub group
         * @param subGroup
         */
        var _toggleSubGroup = function (subGroup, state) {
            subGroup = $('.' + subGroup);
            if (subGroup.length) {
                var fn = state === 'disable' ? 'addClass' : 'removeClass';
                subGroup.data('cover')[fn]('blocking');
            }
        };


        /**
         * enable sub group
         * @param subGroup
         */
        var enableSubGroup = function (subGroup) {
            _toggleSubGroup(subGroup, 'enable');
        };

        /**
         * disable sub group
         * @param subGroup
         */
        var disableSubGroup = function (subGroup) {
            _toggleSubGroup(subGroup, 'disable');
        };

        /**
         * add tooltip to explain special requirement and behaviours for inline interactions
         */
        var addInlineInteractionTooltip = function () {

            var timer,
                $inlineInteractionsPanel = $('#sidebar-left-section-inline-interactions .inline-interactions'),
                $tooltip = $(tooltipTpl({
                    message: __('Inline interactions need to be inserted into a text block.')
                }));

            $inlineInteractionsPanel.append($tooltip);
            tooltip($inlineInteractionsPanel);

            $tooltip.css({
                position: 'absolute',
                zIndex: 11,
                top: 0,
                right: 10
            });

            $inlineInteractionsPanel.on('mouseenter', '.sub-group-cover',function () {

                timer = setTimeout(function () {
                    $tooltip.find('[data-tooltip]').tooltipster('show');
                }, 300);

            }).on('mouseleave', '.sub-group-cover', function () {
                $tooltip.find('[data-tooltip]').tooltipster('hide');
                clearTimeout(timer);
            });
        };

        /**
         * Initialize interface
         */
        var initGui = function (widget) {
            
            var $itemContainer = widget.$container;

            _setupElements();

            adaptHeight();

            buildSubGroups();

            // toggle blocks in sidebar
            // note that this must happen _after_ the height has been adapted
            sidebarAccordionInit();

            // close all
            closeSections(elements.sidebars.find(section));


            /* At the time of writing this the following sections are available:
             *
             * #sidebar-left-section-text
             * #sidebar-left-section-block-interactions
             * #sidebar-left-section-inline-interactions
             * #sidebar-left-section-graphic-interactions
             * #sidebar-left-section-media
             * #sidebar-right-css-manager
             * #sidebar-right-style-editor
             * #sidebar-right-item-properties
             * #sidebar-right-body-element-properties
             * #sidebar-right-text-block-properties
             * #sidebar-right-interaction-properties
             * #sidebar-right-choice-properties
             * #sidebar-right-response-properties
             */

            openSections($('#sidebar-left-section-common-interactions'), false);

            elements.itemPanel.addClass('has-item');

            // display toolbar and sidebar
            //elements.sidebars.add(elements.toolbarInner).fadeTo(2000, 1);
            
            var _scroll = _.throttle(function(e){
                _handleScrolling($itemContainer);
            }, 150);
            
            _scroll();

            $doc.on('scroll', _scroll);
            
            $itemContainer.on('resize', _scroll);
            
            $win.on('resize orientationchange', _scroll);

        };


        return {
            initGui: initGui,
            openSections: openSections,
            closeSections: closeSections,
            adaptHeight: adaptHeight,
            enableSubGroup: enableSubGroup,
            disableSubGroup: disableSubGroup
        };

    }());
    return editor;
});


