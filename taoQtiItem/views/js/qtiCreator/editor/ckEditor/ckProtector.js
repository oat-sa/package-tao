define([
    'jquery',
    'i18n'
], function($, __){
    'use strict';

    /**
     * Protect / unprotect an element to avoid edition by CK EDITOR
     *
     * @returns {{protect: protect, init: init, unprotect: unprotect}}
     */
    var ckeProtector = (function() {

        var selector = '.widget-box';

        /**
         * Observe changes to content editable elements
         *
         * @param context
         */
        var observeContentChanges = function($context) {

            var contextDomNode = $context[0];

            var MutationObserver = (window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver);

            // IE 10
            if (!MutationObserver) {
                contextDomNode.addEventListener('DOMCharacterDataModified', function() {
                    $context.trigger('contentchange.protector');
                }, false);
            }
            // all recent browser apart from IE 10
            else {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function() {
                        $context.trigger('contentchange.protector');
                    })
                });
                observer.observe(contextDomNode, {
                    subtree: true,
                    characterData: true
                });
            }

        };

        /**
         * Retrieve selector from the outside
         *
         * @returns {string}
         */
        var getSelector = function() {
            return selector;
        };

        /**
         * Protect elements from being edited by CKeditor.
         * Global selector can be overridden by any function
         *
         * @param context optional
         * @param selectorParam optional
         */
        var protect = function(context, selectorParam) {
            if(selectorParam) {
                selector = selectorParam;
            }
            context.find(selector).each(function() {
                var widget = $(this),
                    iWidth = widget.outerWidth(),
                    iHeight = widget.outerHeight(),
                    cover = $('<button class="cke-cover-up">'),
                    wrapper,
                    // cke copies the widget to a kind of shadow dom, hence this trickery
                    getProtectedWidgetBySerial = function(serial) {
                        $('[data-serial="' + serial + '"]').each(function() {
                            var widget = $(this);
                            if(widget.parent().hasClass('cke-qti-wrapper')) {
                                return false;
                            }
                        });
                        return widget.length ? widget : $();
                    },
                    positionCover = function(cover) {
                        var wrapper = getProtectedWidgetBySerial(cover.prop('serial')).parent('.cke-qti-wrapper'),
                            iOffset = wrapper.offset();
                        cover.css({
                            left: iOffset.left,
                            top: iOffset.top
                        })
                    };

                if(widget.parent('.cke-qti-wrapper').length) {
                    return false;
                }

                // avoid empty elements since they would be killed by CKE
                // &#8203; means zero-width-space
                widget.find('*').each(function() {
                    if(!this.innerHTML) {
                        this.innerHTML = '&#8203;'
                    }
                });

                widget.wrap($('<span class="cke-qti-wrapper" />'));

                wrapper = widget.parent();

                cover.css({
                    width: iWidth,
                    height: iHeight
                });
                $('body').append(cover);

                wrapper.css({
                    width: iWidth,
                    height: iHeight
                });
                wrapper.attr('contenteditable', false);
                wrapper.prop('cover', cover);

                cover.prop('serial', widget.data('serial'));

                positionCover(cover);

                cover.attr('title', __('Click to display interaction widget'));

                cover.on('click', function() {
                    var widget = getProtectedWidgetBySerial($(this).prop('serial'));
                    //unprotect(widget);
                    $(document).trigger('removeprotection.ckprotector', { context: context, widget: widget });
                });

                observeContentChanges(context);

                context.on('contentchange.protector', function() {
                    positionCover(cover);
                });
            });
        };

        /**
         * Protect protection
         * Global selector can be overridden by any function
         *
         * @param protectedArea optional
         */
        var unprotect = function(widget) {
            widget.each(function() {
                var widget = $(this),
                    wrapper = widget.parent('.cke-qti-wrapper');
                if(wrapper.length) {
                    wrapper.prop('cover').remove();
                    widget.unwrap();
                }
            });
        };

        return {
            protect: protect,
            unprotect: unprotect,
            getSelector: getSelector
        };
    }());
    return ckeProtector;
});


