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
    'taoQtiTest/testRunner/actionBar/button',
    'tpl!taoQtiTest/testRunner/tpl/comment'
], function ($, _, __, button, commentTpl) {
    'use strict';

    /**
     * Events namespace
     * @type {String}
     * @private
     */
    var _ns = '.qtiComment';

    /**
     * Defines an action bar button that add a comment into the current assessment item
     * @type {Object}
     */
    var comment = {
        /**
         * Additional setup onto the button config set
         */
        setup : function setup() {
            var label = __('Comment');
            _.defaults(this.config, {
                label : label,
                title : label,
                icon : 'tag'
            });
            this.config.discard = '[data-control="qti-comment"]';
            this.config.content = commentTpl(this.config);
        },

        /**
         * Additional DOM rendering
         */
        afterRender : function afterRender() {
            var self = this;

            this.$form = this.$button.find('[data-control="qti-comment"]');
            this.$input = this.$button.find('[data-control="qti-comment-text"]');
            this.$cancel = this.$button.find('[data-control="qti-comment-cancel"]');
            this.$submit = this.$button.find('[data-control="qti-comment-send"]');

            this.$cancel.on('click' + _ns, function() {
                self.closeForm();
            });

            this.$submit.on('click' + _ns, function() {
                self.submitForm();
            });
        },

        /**
         * Additional cleaning while uninstalling the button
         * @private
         */
        tearDown : function tearDown() {
            if (this.$form) {
                this.closeForm();
            }
            if (this.$cancel) {
                this.$cancel.off(_ns);
            }
            if (this.$submit) {
                this.$submit.off(_ns);
            }

            this.$form = null;
            this.$input = null;
            this.$cancel = null;
            this.$submit = null;
        },

        /**
         * Action called when the button is clicked
         */
        action : function action() {
            var visible;
            if (this.$form) {
                this.$form.toggleClass('hidden');

                visible = !this.$form.hasClass('hidden');
                this.setActive(visible);

                if (visible) {
                    this.$input.val('').focus();
                }
            }
        },

        /**
         * Closes the "comment" form
         */
        closeForm : function closeForm() {
            this.$form.addClass('hidden');
            this.setActive(false);
        },

        /**
         * Sends the comment to the server
         */
        submitForm : function submitForm() {
            var self = this;
            var comment = this.$input.val();

            if (comment) {
                $.when(
                    $.post(
                        self.testContext.commentUrl,
                        { comment: comment }
                    )
                ).done(function() {
                    self.closeForm();
                });
            }
        },

        /**
         * Tells if the button is visible and can be rendered
         * @returns {Boolean}
         */
        isVisible : function isVisible() {
            return !!this.testContext.allowComment;
        }
    };

    return button(comment);
});
