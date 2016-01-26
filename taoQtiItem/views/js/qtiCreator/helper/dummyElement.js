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
define([
    'jquery',
    'lodash'
], function ($, _) {
    'use strict';

    var dummyElement = (function () {

        var types = {
            maths: {
                icon: 'maths',
                css: {
                    width : 40,
                    height : 22
                }
            },
            image: {
                icon: 'image',
                css: {
                    width : 50,
                    height : 35
                }
            },
            video: {
                icon: 'video',
                css: {
                    width : 200,
                    height : 150
                }
            },
            media: {
                icon: 'media',
                css: {
                    width : 150,
                    height : 100
                }
            },
            include: {
                icon: 'shared-file',
                css: {
                    width : '100%',
                    height : 100
                }
            }
        };

        /**
         * create dummy element
         *
         * Examples:
         *
         * 1. Generic placeholder
         * dummyElement.get();
         * -> <span class="dummy-element" style="width:80px; height: 22px"></span>
         *
         * 2. Pre-defined placeholder
         * dummyElement.get('math|image|video|...')
         * -> <span style="width: 150px; height: 100px; font-size: 80px; padding-top: 9px;" class="dummy-element">
         *       <span class="icon-image"></span>
         *    </span>
         *
         * 3. Freestyle
         * works almost like $('<element>'), except that 'element' and 'css' (both optionally) are part of an object
         * dummyElement.get({ element: 'div', class: 'foo bar', css: { color: 'red'}})
         * -> <div class="dummy-element foo bar" style="color: red; width:80px; height: 22px">
         *       <span class="icon-image"></span>
         *    </div>
         *
         *
         * @param arg {} | string
         * @returns {*|HTMLElement}
         */
        var get = function (arg) {
            var options = {
                element: 'span',
                'class': 'dummy-element',
                css: {
                    width : 80,
                    height : 22
                }
            },
            element,
            $element,
            $icon,
            css,
            finalOptions;

            if(arg) {
                if($.isPlainObject(arg)) {
                    // class names must be added to 'dummy-element'
                    if(arg.class) {
                        options.class += ' ' + arg.class;
                        delete(arg.class);
                    }
                    // 'deep' required to copy CSS correctly
                    options = $.extend(true, {}, options, arg);
                }
                else if (types[arg]) {
                    options = $.extend({}, options, types[arg]);
                }
            }

            // icon
            $icon = options.icon ? $('<span>', { class: 'icon-' + options.icon.replace('icon-', '') }) : false;

            element = '<' + options.element + '>';

            css = _.cloneDeep(options.css);

            // adapt font size to container size
            css['font-size'] = css.height && !css['font-size']
                ? (css.height * .8)
                : 14;

            css['line-height'] = css['font-size'].toString() + 'px';

            if(css['height'] > 30) {
                css['padding-top'] = ((css['height'] - css['font-size']) / 2) * .9;
            }

            // don't scale background-picture on large elements
            if(css.height && css.height > 100) {
                css['background-size'] = 'auto';
            }

            finalOptions = _.cloneDeep(options);
            delete(finalOptions.icon);
            delete(finalOptions.element);
            delete(finalOptions.css);

            $element = $(element, finalOptions).css(css);

            if($icon) {
                $element.append($icon);
            }
            return $element;
        };

        return {
            get: get
        }

    }());
    return dummyElement;
});


