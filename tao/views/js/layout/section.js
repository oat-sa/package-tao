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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */
/**
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
define([
    'jquery',
    'lodash',
    'context',
    'history'
], function(
    $,
    _,
    context,
    history
){
    'use strict';

    var sectionParamExp = /&section=([^&]*)/;
    var location = window.history.location || window.location;

    //back & forward button, and push state
    $(window).on('popstate', function (event) {
        restoreState(getState());
    });

    /**
     * Ensures the state has an identifier and has the right format.
     * @param {Object} state The state to identify
     * @returns {Object} Returns the provided state
     */
    function setStateId(state) {
        var sectionPart, data;

        if (!state || !_.isObject(state)) {
            state = {};
        }

        if (!state.url) {
            state.url = location.href;
        }

        if (!state.id) {
            sectionPart = state.url.match(sectionParamExp);
            state.id = sectionPart && sectionPart[1];
        }

        if (!state.data) {
            state.data = {};
        }
        data = state.data;
        data.sectionId = data.sectionId || state.sectionId || state.id;
        data.restoreWith = data.restoreWith || state.restoreWith || 'activate';

        return state;
    }

    /**
     * Gets the current history state.
     *
     * @returns {Object}
     */
    function getState() {
        var state = history.state;
        return setStateId(state);
    }

    /**
     * Restore a state from the history.
     * It calls activate or show on the section saved into the state.
     * @param {Object} state - a state that has been pushed previously
     * @returns {Boolean|SectionApi} false if there is nothing to restore
     */
    function restoreState(state){
        if(state && state.data && state.data.sectionId){
           return sectionApi.get(state.data.sectionId)['_' + state.data.restoreWith]();
        }
    }


    /**
     * Add a new state to the history
     * @param {Object} section
     * @param {String} [restoreWith = 'activate']
     */
    function pushState(section, restoreWith){
        var stateUrl;
        var stateUrlWithoutSection;
        var hasNoSection;
        var method;

        if (section) {
            stateUrl = window.location.search + '' || '?';
            stateUrlWithoutSection = stateUrl.replace(sectionParamExp, '');
            hasNoSection = stateUrl === stateUrlWithoutSection;
            method = hasNoSection ? 'replaceState' : 'pushState';

            history[method]({
                    sectionId : section.id,
                    restoreWith : restoreWith || 'activate'
                },
                section.name || '',
                stateUrlWithoutSection + '&section=' + section.id
            );
            restoreState(getState());
        }
    }

    /**
     * The section API provides you all the methods needed to manage sections.
     * @typedef SectionApi
     * @exports layout/section
     */
    var sectionApi = {

        scope : $('.section-container'),
        sections : {},
        selected : null,

        /**
         * Find section into the scope and initiliaze them.
         *
         * @param {jQueryElement} $scope - the main scope
         * @param {Object} [options] - configuration options
         * @param {Boolean} [options.history = true] - use the history manager
         * @returns {SectionApi} instance for chaining
         *
         * @fires SectionApi#init.section
         */
        init : function($scope, options){
            var self = this;
            var restore = true;
            var $openersContainer;
            var defaultSection;

            var paramResult = window.location.toString().match(sectionParamExp);
            if(paramResult && paramResult.length){
                defaultSection = paramResult[1].replace('#', '');
            }

            this.options = options || {};

            this.scope = $scope || this.scope || $('.section-container');
            $openersContainer = $('.tab-container', this.scope);

            this.selected = null;

            //load sections from the DOM
            $('li', $openersContainer).each(function(index){

                 var $sectionOpener = $(this);
                 var $link = $sectionOpener.children('a');
                 var id = $link.attr('href').replace('#panel-', '');
                 var $panel = $('#panel-' + id);
                 var active = false;

                $panel.removeClass('hidden');

                 self.sections[id] = {
                    id          : id,
                    url         : $link.data('url'),
                    name        : $link.text(),
                    panel       : $('#panel-' + id),
                    opener      : $sectionOpener,
                    type        : $panel.find('.section-trees').children().length ? 'tree' : 'content',
                    active      : defaultSection ? defaultSection === id : index === 0,
                    activated   : false,
                    disabled    : $sectionOpener.hasClass('disabled')
                 };
            });

            //to be sure at least one is active, for example when the given default section does not exists
            if(_(this.sections).where({'active' : true }).size() === 0){
                for(var id in this.sections){
                    this.sections[id].active =  true;
                    restore = false;
                    break;
                }
            }

            this._bindOpener($openersContainer);

            /**
             * Once the sections are initialized
             * @event SectionApi#init.section
             */
            this.scope.trigger('init.section');


            if(this.options.history === false || !restore || !restoreState(getState())){
                return this.activate();
            }
            return this;
        },

        /**
         * Bind the openeers (ie. the tabs) to react on click.
         * Also hide them if there is less than 1 visible.
         * @param {jQueryElement} $openersContainer - the element that contains the openers
         * @returns {SectionApi} instance for chaining
         */
        _bindOpener : function($openersContainer){
            var self = this;
            //bind click on openers
            $openersContainer
                .off('click.section', 'li')
                .on('click.section', 'li', function(e){
                     e.preventDefault();
                     var id = $(this).children('a').attr('href').replace('#panel-', '');
                     var section = self.sections[id];
                     if(!section.disabled){
                         self.get(id).activate();
                     }
                });

            //display the openers only if there is more than 1 section
            if($('li:not(.hidden)', $openersContainer).length < 2){
                $openersContainer.hide();
            } else {
                $openersContainer.show();
            }

            return this;
        },

        /**
         * Activate the selected current section (by pushing a new state to the history)
         *
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#activate.section
         * @fires SectionApi#hide.section
         * @fires SectionApi#show.section
         */
        activate : function(){
            if(!this.selected){
                this.current();
            }

            if(this.options.history === false){
                return this._activate();
            }

            pushState(this.selected, 'activate');

            return this;
        },

        /**
         * Activate the selected section.
         * Unlike the public one, this method does the job.
         *
         * @private
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#activate.section
         * @fires SectionApi#hide.section
         * @fires SectionApi#show.section
         */
        _activate : function(){

            this._show();
            if(this.selected.activated === false){
                this.selected.activated = true;

                /**
                 * A section is activated
                 * @event SectionApi#activate.section
                 * @param {Object} section - the section
                 */
                this.scope.trigger('activate.section', [this.selected]);
            }

            return this;
        },

        /**
         * Shows the selected section (by pushing a new state to the history).
         * Shows is different from activate just by the events
         * that are send (show doesn't trigger the activate event).
         *
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#hide.section
         * @fires SectionApi#show.section
         */
        show : function(){
            if(!this.selected){
                this.current();
            }

            if(this.options.history === false){
                return this._show();
            }

            pushState(this.selected, 'show');

            return this;
        },


        /**
         * Shows the selected section.
         * Unlike the public one, this method does the job.
         *
         * @private
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#hide.section
         * @fires SectionApi#show.section
         */
        _show : function(){

            var self = this;
            var active = _(this.sections).where({'active' : true }).first();

            //switch the active section if set previously
            if(this.selected && this.selected.id !== active.id){
                _.forEach(this.sections, function(section){
                    section.active = false;
                });
                this.sections[this.selected.id].active = true;
             } else {
                this.current();
            }

            _.where(this.sections, {'active' : false }).forEach(function(section){
                section.opener.removeClass('active');
                section.panel.hide();

                /**
                 * A section is hidden
                 * @event SectionApi#hide.section
                 * @param {Object} section - the section
                 */
                self.scope.trigger('hide.section', [section]);

            });
            _.where(this.sections, {'active' : true }).forEach(function(section){
                section.opener.addClass('active');
                section.panel.show();

                /**
                 * A section is shown
                 * @event SectionApi#show.section
                 * @param {Object} section - the section
                 */
                self.scope.trigger('show.section', [section]);
            });

            return this;
        },

        /**
         * refresh the sections.
         * they are re loaded from the dom.
         *
         * @returns {sectionapi} instance for chaining
         */
        refresh : function(){
            this.sections = {};
            return this.init();
        },

        /**
         * Enable the current section
         *
         * @returns {sectionapi} instance for chaining
         * @fires SectionApi#enable.section
         */
        enable : function(){
            if(!this.selected){
                this.current();
            }
            if(this.selected.disabled === true){
                this.selected.disabled = false;
                this.selected.opener.removeClass('disabled');

                /**
                 * A section is enabled
                 * @event SectionApi#enable.section
                 * @param {Object} section - the section
                 */
                this.scope.trigger('enable.section', [this.selected]);
            }
            return this;
        },

        /**
         * Disable the current section
         *
         * @returns {sectionapi} instance for chaining
         * @fires SectionApi#disable.section
         */
        disable : function(){
            if(!this.selected){
                this.current();
            }
            if(this.selected.disabled === false){
                this.selected.disabled = true;
                this.selected.opener.addClass('disabled');

                /**
                 * A section is disabled
                 * @event SectionApi#disable.section
                 * @param {Object} section - the section
                 */
                this.scope.trigger('disable.section', [this.selected]);
            }
            return this;
        },

        /**
         * Make the active section the selected. Useful before chaining with another method :
         * @example section.current().show();
         *
         *
         * @returns {SectionApi} instance for chaining
         */
        current : function(){
            this.selected =  _(this.sections).where({'active' : true }).first();
            return this;
        },

        /**
         * This method enables you to create a new section.
         * If the section already exists, it may be updated (panel's content)
         *
         * @param {Object} data - the section data
         * @param {String} data.id - the section identifier
         * @param {String} data.url - the section url
         * @param {String} data.name - the section name (already translated please)
         * @param {Boolean} [data.visible] - is the section opener (ie. the tab) shown ?
         * @param {String} [data.content] - the panel content
         *
         * @returns {SectionApi} instance for chaining
         */
        create : function(data){
            var $openersContainer = this.scope.find('.tab-container');
            var $sectionOpener,
                $sectionPanel,
                section;

            if(!_.isObject(data)){
                throw new TypeError("The create() method requires an object with section data as parameter.");
            }
            if(!_.isString(data.id) || !_.isString(data.url) || !_.isString(data.name)){
                throw new TypeError("The create() method requires data with id, url and name to create a new section.");
            }
            if(typeof data.visible === 'undefined'){
                data.visible = true;
            }

            this.get(data.id);
            section = this.selected && this.selected.id === data.id ? this.selected : undefined;


            if(!section){

                //TODO use templates
                $sectionPanel = $('<div id="panel-' + data.id +'" class="clear"></div>');
                if(data.contentBlock === true){
                    $sectionPanel.append('<section class="content-container"><ul class="plain action-bar content-action-bar horizontal-action-bar"></ul><div class="content-block"></div></section>');
                }
                $sectionOpener = $('<li class="small ' + (!data.visible ? 'hidden' : '') +'"><a title="'+data.name+'" data-url="'+data.url+'" href="#panel-' + data.id +'">'+data.name+'</a></li>');
                $openersContainer.append($sectionOpener);
                this.scope.append($sectionPanel);



                section =  {
                    id          : data.id,
                    url         : data.url,
                    name        : data.name,
                    panel       : $sectionPanel,
                    opener      : $sectionOpener,
                    type        : 'content',
                    active      : false
                };
                this.sections[data.id] = section;
            }
            section.url = section.url === data.url || data.url === undefined ? section.url : data.url;
            this.selected = section;

            if(data.content){
                if(data.contentBlock === true){
                    this.updateContentBlock(data.content);
                } else {
                    section.panel.html(data.content);
                }

            } else {
                if(data.contentBlock === true){
                    this.loadContentBlock();
                } else {
                    this.load();
                }
            }

            this._bindOpener($openersContainer);

            return this;
        },

        /**
         * Select a section using either it's id or url.
         *
         * @example section.get('manage_items').activate();
         *
         * @param {String} value - id, panel id, short or long URL
         * @returns {SectionApi} instance for chaining
         */
        get : function(value){
            var section;
            if(!_.isString(value)){
                throw new TypeError("The get() method requires a string parameter, the section id or url.");
            }

            //try to get the section assuming the value is the id or the url.
            section =
                this.sections[value] ||
                this.sections[value.replace('panel-', '')] ||
                _(this.sections).where({'url' : value }).first() ||
                _(this.sections).where({'url' : context.root_url + value }).first();
            if(section){
                this.selected = section;
            } else {
                this.current();
            }

            return this;
        },

        /**
         * Loads content from a URL to the section's panel.
         *
         * @example section.get('manage_items').load();
         *
         * @param {String} [url] - the url to load, by default section's URL is used.
         * @param {Object} [data] - data to add to the request
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#load.section
         */
        load : function(url, data){
            var self = this;
            var wideDifferenciator = '[data-content-target="wide"]';
            var $contentBlock;

            if(!this.selected){
                this.current();
            }
            url = url || this.selected.url;

            if(this.selected.type === 'tree'){
                this.selected.panel.addClass('content-panel');
            } else {
                this.selected.panel.removeClass('content-panel');
            }

            this.selected.panel.empty().load(url, data, function(response){

                /**
                 * Section content has been loaded
                 * @event SectionApi#load.section
                 * @param {Object} section - the section
                 * @param {String} response - the received content
                 */
                self.scope.trigger('load.section', [self.selected, response]);
            });

            return this;
        },

        /**
         * Loads content from a URL but try to target first the content block area before the panel.
         *
         * @example section.get('manage_items').loadContentBlock('/taoItems/Items/edit');
         *
         * @param {String} [url] - the url to load, by default section's URL is used.
         * @param {Object} [data] - data to add to the request
         * @returns {SectionApi} instance for chaining
         * @fires SectionApi#load.section
         */
        loadContentBlock : function(url, data){
            var $contentblock;

            if(!this.selected){
                this.current();
            }
            url = url || this.selected.url;

            if(this.selected.type === 'tree'){
                this.selected.panel.addClass('content-panel');
            } else {
                this.selected.panel.removeClass('content-panel');
            }

            $contentblock = $('.content-block', this.selected.panel);

            if($contentblock.length){

                //do not yet trigger event on content block load, but may be required
                $contentblock.empty().load(url, data);
                return this;
            }

            return this.load(url, data);
        },

        /**
         * Update content block's content or the panel if not found.
         *
         * @param {String} [html] - the new content
         * @returns {SectionApi} instance for chaining
         */
        updateContentBlock : function(html){
            var $contentblock = $('.content-block', this.selected.panel);
            if($contentblock.length){
                $contentblock.empty().html(html);
            } else {
                this.selected.panel.empty().html(html);
            }
            return this;
        },

        /**
         * Sugar to help you listen for event on sections
         *
         * @param {String} eventName - the name of the event (without the namespace)
         * @param {Function} cg - the event callback
         * @returns {SectionApi} instance for chaining
         */
        on : function(eventName, cb){
            var self = this;
            this.scope.on(eventName + '.section', function(e){
                cb.apply(self, Array.prototype.slice.call(arguments, 1));
            });
            return this;
        },

        /**
         * Sugar to help you remove listeners from sections
         *
         * @param {String} eventName - the name of the event (without the namespace)
         * @returns {SectionApi} instance for chaining
         */
        off : function(eventName){
            this.scope.off(eventName + '.section');
            return this;
        }
    };

    return sectionApi;
});
