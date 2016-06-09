/**
 * RequireJS implementation of https://github.com/matthewmueller/unmatrix
 */
define([], function () {
    'use strict';


    /**
     * Unmatrix
     *
     * @param {Element|String} input || matrix thereof
     * @return {Object}
     */
    function unmatrix(input) {
        return 'string' !== typeof input ?
            parse(style(input)) :
            parse(input);
    }

    /**
     * Unmatrix: parse the values of the matrix
     *
     * Algorithm from:
     *
     * - http://hg.mozilla.org/mozilla-central/file/7cb3e9795d04/layout/style/nsStyleAnimation.cpp
     *
     * @param {String} str
     * @return {Object}
     * @api public
     */
    function parse(str) {

        if(str === 'none') {
            return {
                translateX: 0,
                translateY: 0,
                rotate: 0,
                skew: 0,
                scaleX: 1,
                scaleY: 1
            };
        }

        var m = stom(str);
        var A = m[0];
        var B = m[1];
        var C = m[2];
        var D = m[3];

        if (A * D === B * C) {
            throw new Error('transform#unmatrix: matrix is singular');
        }

        // step (3)
        var scaleX = Math.sqrt(A * A + B * B);
        A /= scaleX;
        B /= scaleX;

        // step (4)
        var skew = A * C + B * D;
        C -= A * skew;
        D -= B * skew;

        // step (5)
        var scaleY = Math.sqrt(C * C + D * D);
        C /= scaleY;
        D /= scaleY;
        skew /= scaleY;

        // step (6)
        if ( A * D < B * C ) {
            A = -A;
            B = -B;
            skew = -skew;
            scaleX = -scaleX;
        }

        return {
            translateX: m[4],
            translateY: m[5],
            rotate: rtod(Math.atan2(B, A)),
            skew: rtod(Math.atan(skew)),
            scaleX: round(scaleX),
            scaleY: round(scaleY)
        };
    }

    /**
     * Get the computed style
     *
     * @param {Element} el
     * @return {String}
     * @api private
     */
    function style(el) {
        var _style =  window.getComputedStyle(el);

        return _style.getPropertyValue('transform') ||
            _style.getPropertyValue('-webkit-transform') ||
            _style.getPropertyValue('-ms-transform');
    }

    /**
     * String to matrix
     *
     * @param {String} style
     * @return {Array}
     * @api private
     */
    function stom(str) {

        var m = [];

        if (window.WebKitCSSMatrix) {
            m = new window.WebKitCSSMatrix(str);
            return [m.a, m.b, m.c, m.d, m.e, m.f];
        }

        var rdigit = /[\d\.\-]+/g;
        var n;

        while(n = rdigit.exec(str)) {
            m.push(+n);
        }
        return m;
    }

    /**
     * Radians to degrees
     *
     * @param {Number} radians
     * @return {Number} degrees
     * @api private
     */

    function rtod(radians) {
        var deg = radians * 180 / Math.PI;
        return round(deg);
    }

    /**
     * Round to the nearest hundredth
     *
     * @param {Number} n
     * @return {Number}
     * @api private
     */
    function round(n) {
        return Math.round(n * 100) / 100;
    }


    /**
     * @exports
     */
    return unmatrix;
});
