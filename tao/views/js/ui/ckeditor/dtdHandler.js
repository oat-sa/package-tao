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
    'lodash'
], function (_) {
    'use strict';

    /**
     * Singleton to edit the XHTML DTD and to retrieve element relationships.
     * The DTD code itself is almost 1:1 copied from CKEDITOR
     */
    var dtdHandler = (function () {

        var dtdMode = 'html',
            dtds = {};


        /**
         * Set DTD mode (qti|html)
         */
        var setMode = function (mode) {
            // for the oblivious ones such as myself:
            if(mode.toLowerCase() === 'xhtml'){
                mode = 'html';
            }
            if (!_.contains(['html', 'qti'], mode)) {
                throw new Error('Unknown mode ' + mode)
            }
            dtdMode = mode;
            return this;
        };

        /**
         * Elements that are present in HTML 5 only
         * @type {string[]}
         */
        var html5Only = [
            'article', 'aside', 'bdi', 'command', 'datalist', 'details', 'dialog',
            'figcaption', 'figure', 'footer', 'header', 'keygen', 'main', 'mark',
            'meter', 'nav', 'output', 'progress', 'rp', 'rt', 'ruby', 'section',
            'summary', 'time', 'wbr', 'hgroup'
        ];


        /**
         * Check in which element this element can be contained
         *
         * @param child (string|DOM element|jQuery element)
         * @returns {Array}
         */
        var getParentsOf = function (child) {
            var parents = [],
                element;

            child = _normalizeElement(child);

            for (element in dtds[dtdMode]) {
                if (!dtds[dtdMode].hasOwnProperty(element)) {
                    continue;
                }
                if (element.indexOf('$') === 0) {
                    continue;
                }
                if (child in dtds[dtdMode][element]) {
                    parents.push(element);
                }
            }
            return parents;
        };


        /**
         * Get all elements parent can contain
         *
         * @param parent (string|DOM element|jQuery element)
         * @returns {*}
         */
        var getChildrenOf = function (parent) {

            parent = _normalizeElement(parent);

            if (parent in dtds[dtdMode]) {
                return _.keys(dtds[dtdMode][parent]);
            }

            return [];
        };


        /**
         * Finds whether a child can have a certain parent
         *
         * @param child (string|DOM element|jQuery element)
         * @param of (string|DOM element|jQuery element)
         * @returns {*}
         */
        var isChildOf = function (child, of) {
            return _.contains(getChildrenOf(of), _normalizeElement(child));
        };


        /**
         * Finds whether a parent can have a certain child
         *
         * @param parent (string|DOM element|jQuery element)
         * @param of (string|DOM element|jQuery element)
         * @returns {*}
         */
        var isParentOf = function (parent, of) {
            return _.contains(getParentsOf(of), _normalizeElement(parent));
        };


        /**
         * Retrieve the current DTD
         *
         * @returns {*}
         */
        var getDtd = function () {
            return dtds[dtdMode];
        };


        /**
         * Retrieve the current dtdMode (qti|html)
         *
         * @returns {string}
         */
        var getMode = function () {
            return dtdMode;
        };

        /**
         * Convert (node)element into string
         *
         * @param element (string|DOM element|jQuery element)
         * @returns {*}
         * @private
         */
        var _normalizeElement = function (element) {

            // jQuery or DOM element
            if (_.isObject(element) && !_.isArray(element)) {
                // DOM element
                if ('nodeName' in element) {
                    return element.nodeName.toLowerCase();
                }
                // jQuery element
                else if (0 in element && 'nodeName' in element[0]) {
                    return element[0].nodeName.toLowerCase();
                }
            }
            // node name
            else if (_.isString(element)) {
                return element.toLowerCase();
            }
            // invalid input
            throw new Error('Unknown element ' + element);
        };


        /**
         * This part is almost literally copied from CKEDITOR (apart from using lodash instaed of CKEDITOR tools)
         *
         * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
         * For licensing, see LICENSE.md or http://ckeditor.com/license
         */
        dtds.html = (function () {
            'use strict';

            var X = _.extend,
                Y = function (source, removed) {
                    var substracted = _.cloneDeep(source);
                    for (var i = 1; i < arguments.length; i++) {
                        removed = arguments[i];
                        for (var name in removed)
                            delete substracted[name];
                    }
                    return substracted;
                };

            // Phrasing elements.
            // P = { a: 1, em: 1, strong: 1, small: 1, abbr: 1, dfn: 1, i: 1, b: 1, s: 1,
            //      u: 1, code: 1, 'var': 1, samp: 1, kbd: 1, sup: 1, sub: 1, q: 1, cite: 1,
            //      span: 1, bdo: 1, bdi: 1, br: 1, wbr: 1, ins: 1, del: 1, img: 1, embed: 1,
            //      object: 1, iframe: 1, map: 1, area: 1, script: 1, noscript: 1, ruby: 1,
            //      video: 1, audio: 1, input: 1, textarea: 1, select: 1, button: 1, label: 1,
            //      output: 1, keygen: 1, progress: 1, command: 1, canvas: 1, time: 1,
            //      meter: 1, detalist: 1 },

            // Flow elements.
            // F = { a: 1, p: 1, hr: 1, pre: 1, ul: 1, ol: 1, dl: 1, div: 1, h1: 1, h2: 1,
            //      h3: 1, h4: 1, h5: 1, h6: 1, hgroup: 1, address: 1, blockquote: 1, ins: 1,
            //      del: 1, object: 1, map: 1, noscript: 1, section: 1, nav: 1, article: 1,
            //      aside: 1, header: 1, footer: 1, video: 1, audio: 1, figure: 1, table: 1,
            //      form: 1, fieldset: 1, menu: 1, canvas: 1, details:1 },

            // Text can be everywhere.
            // X( P, T );
            // Flow elements set consists of phrasing elements set.
            // X( F, P );

            var P = {}, F = {},
            // Intersection of flow elements set and phrasing elements set.
                PF = {
                    a: 1,
                    abbr: 1,
                    area: 1,
                    audio: 1,
                    b: 1,
                    bdi: 1,
                    bdo: 1,
                    br: 1,
                    button: 1,
                    canvas: 1,
                    cite: 1,
                    code: 1,
                    command: 1,
                    datalist: 1,
                    del: 1,
                    dfn: 1,
                    em: 1,
                    embed: 1,
                    i: 1,
                    iframe: 1,
                    img: 1,
                    input: 1,
                    ins: 1,
                    kbd: 1,
                    keygen: 1,
                    label: 1,
                    map: 1,
                    mark: 1,
                    meter: 1,
                    noscript: 1,
                    object: 1,
                    output: 1,
                    progress: 1,
                    q: 1,
                    ruby: 1,
                    s: 1,
                    samp: 1,
                    script: 1,
                    select: 1,
                    small: 1,
                    span: 1,
                    strong: 1,
                    sub: 1,
                    sup: 1,
                    textarea: 1,
                    time: 1,
                    u: 1,
                    'var': 1,
                    video: 1,
                    wbr: 1
                },
            // F - PF (Flow Only).
                FO = {
                    address: 1,
                    article: 1,
                    aside: 1,
                    blockquote: 1,
                    details: 1,
                    div: 1,
                    dl: 1,
                    fieldset: 1,
                    figure: 1,
                    footer: 1,
                    form: 1,
                    h1: 1,
                    h2: 1,
                    h3: 1,
                    h4: 1,
                    h5: 1,
                    h6: 1,
                    header: 1,
                    hgroup: 1,
                    hr: 1,
                    menu: 1,
                    nav: 1,
                    ol: 1,
                    p: 1,
                    pre: 1,
                    section: 1,
                    table: 1,
                    ul: 1
                },
            // Metadata elements.
                M = {
                    command: 1,
                    link: 1,
                    meta: 1,
                    noscript: 1,
                    script: 1,
                    style: 1
                },
            // Empty.
                E = {},
            // Text.
                T = {
                    '#': 1
                },

            // Deprecated phrasing elements.
                DP = {
                    acronym: 1,
                    applet: 1,
                    basefont: 1,
                    big: 1,
                    font: 1,
                    isindex: 1,
                    strike: 1,
                    style: 1,
                    tt: 1
                }, // TODO remove "style".
            // Deprecated flow only elements.
                DFO = {
                    center: 1,
                    dir: 1,
                    noframes: 1
                };

            // Phrasing elements := PF + T + DP
            X(P, PF, T, DP);
            // Flow elements := FO + P + DFO
            X(F, FO, P, DFO);

            var dtd = {
                a: Y(P, {
                    a: 1,
                    button: 1
                }), // Treat as normal inline element (not a transparent one).
                abbr: P,
                address: F,
                area: E,
                article: X({
                    style: 1
                }, F),
                aside: X({
                    style: 1
                }, F),
                audio: X({
                    source: 1,
                    track: 1
                }, F),
                b: P,
                base: E,
                bdi: P,
                bdo: P,
                blockquote: F,
                body: F,
                br: E,
                button: Y(P, {
                    a: 1,
                    button: 1
                }),
                canvas: P, // Treat as normal inline element (not a transparent one).
                caption: F,
                cite: P,
                code: P,
                col: E,
                colgroup: {
                    col: 1
                },
                command: E,
                datalist: X({
                    option: 1
                }, P),
                dd: F,
                del: P, // Treat as normal inline element (not a transparent one).
                details: X({
                    summary: 1
                }, F),
                dfn: P,
                div: X({
                    style: 1
                }, F),
                dl: {
                    dt: 1,
                    dd: 1
                },
                dt: F,
                em: P,
                embed: E,
                fieldset: X({
                    legend: 1
                }, F),
                figcaption: F,
                figure: X({
                    figcaption: 1
                }, F),
                footer: F,
                form: F,
                h1: P,
                h2: P,
                h3: P,
                h4: P,
                h5: P,
                h6: P,
                head: X({
                    title: 1,
                    base: 1
                }, M),
                header: F,
                hgroup: {
                    h1: 1,
                    h2: 1,
                    h3: 1,
                    h4: 1,
                    h5: 1,
                    h6: 1
                },
                hr: E,
                html: X({
                    head: 1,
                    body: 1
                }, F, M), // Head and body are optional...
                i: P,
                iframe: T,
                img: E,
                input: E,
                ins: P, // Treat as normal inline element (not a transparent one).
                kbd: P,
                keygen: E,
                label: P,
                legend: P,
                li: F,
                link: E,
                map: F,
                mark: P, // Treat as normal inline element (not a transparent one).
                menu: X({
                    li: 1
                }, F),
                meta: E,
                meter: Y(P, {
                    meter: 1
                }),
                nav: F,
                noscript: X({
                    link: 1,
                    meta: 1,
                    style: 1
                }, P), // Treat as normal inline element (not a transparent one).
                object: X({
                    param: 1
                }, P), // Treat as normal inline element (not a transparent one).
                ol: {
                    li: 1
                },
                optgroup: {
                    option: 1
                },
                option: T,
                output: P,
                p: P,
                param: E,
                pre: P,
                progress: Y(P, {
                    progress: 1
                }),
                q: P,
                rp: P,
                rt: P,
                ruby: X({
                    rp: 1,
                    rt: 1
                }, P),
                s: P,
                samp: P,
                script: T,
                section: X({
                    style: 1
                }, F),
                select: {
                    optgroup: 1,
                    option: 1
                },
                small: P,
                source: E,
                span: P,
                strong: P,
                style: T,
                sub: P,
                summary: P,
                sup: P,
                table: {
                    caption: 1,
                    colgroup: 1,
                    thead: 1,
                    tfoot: 1,
                    tbody: 1,
                    tr: 1
                },
                tbody: {
                    tr: 1
                },
                td: F,
                textarea: T,
                tfoot: {
                    tr: 1
                },
                th: F,
                thead: {
                    tr: 1
                },
                time: Y(P, {
                    time: 1
                }),
                title: T,
                tr: {
                    th: 1,
                    td: 1
                },
                track: E,
                u: P,
                ul: {
                    li: 1
                },
                'var': P,
                video: X({
                    source: 1,
                    track: 1
                }, F),
                wbr: E,

                // Deprecated tags.
                acronym: P,
                applet: X({
                    param: 1
                }, F),
                basefont: E,
                big: P,
                center: F,
                dialog: E,
                dir: {
                    li: 1
                },
                font: P,
                isindex: E,
                noframes: F,
                strike: P,
                tt: P
            };

            X(dtd, {
                /**
                 * List of block elements, like `<p>` or `<div>`.
                 */
                $block: X({
                    audio: 1,
                    dd: 1,
                    dt: 1,
                    figcaption: 1,
                    li: 1,
                    video: 1
                }, FO, DFO),

                /**
                 * List of elements that contain other blocks, in which block-level operations should be limited,
                 * this property is not intended to be checked directly, use {@link CKEDITOR.dom.elementPath#blockLimit} instead.
                 *
                 * Some examples of editor behaviors that are impacted by block limits:
                 *
                 * * Enter key never split a block-limit element;
                 * * Style application is constraint by the block limit of the current selection.
                 * * Pasted html will be inserted into the block limit of the current selection.
                 *
                 * **Note:** As an exception `<li>` is not considered as a block limit, as it's generally used as a text block.
                 */
                $blockLimit: {
                    article: 1,
                    aside: 1,
                    audio: 1,
                    body: 1,
                    caption: 1,
                    details: 1,
                    dir: 1,
                    div: 1,
                    dl: 1,
                    fieldset: 1,
                    figcaption: 1,
                    figure: 1,
                    footer: 1,
                    form: 1,
                    header: 1,
                    hgroup: 1,
                    menu: 1,
                    nav: 1,
                    ol: 1,
                    section: 1,
                    table: 1,
                    td: 1,
                    th: 1,
                    tr: 1,
                    ul: 1,
                    video: 1
                },

                /**
                 * List of elements that contain character data.
                 */
                $cdata: {
                    script: 1,
                    style: 1
                },

                /**
                 * List of elements that are accepted as inline editing hosts.
                 */
                $editable: {
                    address: 1,
                    article: 1,
                    aside: 1,
                    blockquote: 1,
                    body: 1,
                    details: 1,
                    div: 1,
                    fieldset: 1,
                    figcaption: 1,
                    footer: 1,
                    form: 1,
                    h1: 1,
                    h2: 1,
                    h3: 1,
                    h4: 1,
                    h5: 1,
                    h6: 1,
                    header: 1,
                    hgroup: 1,
                    nav: 1,
                    p: 1,
                    pre: 1,
                    section: 1
                },

                /**
                 * List of empty (self-closing) elements, like `<br>` or `<img>`.
                 */
                $empty: {
                    area: 1,
                    base: 1,
                    basefont: 1,
                    br: 1,
                    col: 1,
                    command: 1,
                    dialog: 1,
                    embed: 1,
                    hr: 1,
                    img: 1,
                    input: 1,
                    isindex: 1,
                    keygen: 1,
                    link: 1,
                    meta: 1,
                    param: 1,
                    source: 1,
                    track: 1,
                    wbr: 1
                },

                /**
                 * List of inline (`<span>` like) elements.
                 */
                $inline: P,

                /**
                 * List of list root elements.
                 */
                $list: {
                    dl: 1,
                    ol: 1,
                    ul: 1
                },

                /**
                 * List of list item elements, like `<li>` or `<dd>`.
                 */
                $listItem: {
                    dd: 1,
                    dt: 1,
                    li: 1
                },

                /**
                 * List of elements which may live outside body.
                 */
                $nonBodyContent: X({
                    body: 1,
                    head: 1,
                    html: 1
                }, dtd.head),

                /**
                 * Elements that accept text nodes, but are not possible to edit into the browser.
                 */
                $nonEditable: {
                    applet: 1,
                    audio: 1,
                    button: 1,
                    embed: 1,
                    iframe: 1,
                    map: 1,
                    object: 1,
                    option: 1,
                    param: 1,
                    script: 1,
                    textarea: 1,
                    video: 1
                },

                /**
                 * Elements that are considered objects, therefore selected as a whole in the editor.
                 */
                $object: {
                    applet: 1,
                    audio: 1,
                    button: 1,
                    hr: 1,
                    iframe: 1,
                    img: 1,
                    input: 1,
                    object: 1,
                    select: 1,
                    table: 1,
                    textarea: 1,
                    video: 1
                },

                /**
                 * List of elements that can be ignored if empty, like `<b>` or `<span>`.
                 */
                $removeEmpty: {
                    abbr: 1,
                    acronym: 1,
                    b: 1,
                    bdi: 1,
                    bdo: 1,
                    big: 1,
                    cite: 1,
                    code: 1,
                    del: 1,
                    dfn: 1,
                    em: 1,
                    font: 1,
                    i: 1,
                    ins: 1,
                    label: 1,
                    kbd: 1,
                    mark: 1,
                    meter: 1,
                    output: 1,
                    q: 1,
                    ruby: 1,
                    s: 1,
                    samp: 1,
                    small: 1,
                    span: 1,
                    strike: 1,
                    strong: 1,
                    sub: 1,
                    sup: 1,
                    time: 1,
                    tt: 1,
                    u: 1,
                    'var': 1
                },

                /**
                 * List of elements that have tabindex set to zero by default.
                 */
                $tabIndex: {
                    a: 1,
                    area: 1,
                    button: 1,
                    input: 1,
                    object: 1,
                    select: 1,
                    textarea: 1
                },

                /**
                 * List of elements used inside the `<table>` element, like `<tbody>` or `<td>`.
                 */
                $tableContent: {
                    caption: 1,
                    col: 1,
                    colgroup: 1,
                    tbody: 1,
                    td: 1,
                    tfoot: 1,
                    th: 1,
                    thead: 1,
                    tr: 1
                },

                /**
                 * List of "transparent" elements. See [W3C's definition of "transparent" element](http://dev.w3.org/html5/markup/terminology.html#transparent).
                 */
                $transparent: {
                    a: 1,
                    audio: 1,
                    canvas: 1,
                    del: 1,
                    ins: 1,
                    map: 1,
                    noscript: 1,
                    object: 1,
                    video: 1
                },

                /**
                 * List of elements that are not to exist standalone that must live under its parent element.
                 */
                $intermediate: {
                    caption: 1,
                    colgroup: 1,
                    dd: 1,
                    dt: 1,
                    figcaption: 1,
                    legend: 1,
                    li: 1,
                    optgroup: 1,
                    option: 1,
                    rp: 1,
                    rt: 1,
                    summary: 1,
                    tbody: 1,
                    td: 1,
                    tfoot: 1,
                    th: 1,
                    thead: 1,
                    tr: 1
                }
            });

            return dtd;
        })();


        /**
         * Applies QTI rules to a copy of the xhtmlDtd
         *
         * @return dtd {object} the modified dtd
         */
        dtds.qti = (function () {
            var element,
                listCnt,
                child,
                actions = ['remove', 'add'],
                actCnt,
                actLnt = actions.length,
                action,
                overrides = {
                    pre: {
                        add: [],
                        remove: ['img', 'object', 'big', 'small', 'sub', 'sup']
                    },
                    table: {
                        remove: ['col']
                    }
                },
                qtiDtd = _.cloneDeep(dtds.html),
                tmp,
                h5Len = html5Only.length,
                intersection;

            // remove html5-only keys
            while (h5Len--) {
                delete(qtiDtd[html5Only[h5Len]]);
            }

            // find html5-only elements in children and add them to overrides
            for (element in qtiDtd) {
                if (!qtiDtd.hasOwnProperty(element)) {
                    continue;
                }
                intersection = _.intersection(_.keys(qtiDtd[element]), html5Only);
                if (!intersection.length) {
                    continue;
                }
                if (!overrides[element]) {
                    overrides[element] = {
                        remove: []
                    };
                }
                overrides[element].remove = overrides[element].remove.concat(intersection);
            }

            // execute overrides
            for (element in overrides) {
                if (!overrides.hasOwnProperty(element)) {
                    continue;
                }
                // disallow adding keys to the dtd ckeditor cannot handle
                if (!qtiDtd.hasOwnProperty(element)) {
                    continue;
                }

                // note: removing and adding is on purpose done in two steps
                for (actCnt = 0; actCnt < actLnt; actCnt++) {
                    action = actions[actCnt];
                    if (!(action in overrides[element])) {
                        continue;
                    }
                    listCnt = overrides[element][action].length;


                    // allow 'all' as a shortcut for 'remove all children'
                    if (action === 'remove' && overrides[element][action] === 'all') {
                        qtiDtd[element] = {};
                        continue;
                    }


                    // doggy style loop over children to add
                    while (listCnt--) {
                        child = overrides[element][action][listCnt];
                        // there was some weird behaviour with references
                        // deleting from pre would also delete from p
                        // going over a tmp var solves this, though I have no idea why
                        tmp = _.cloneDeep(qtiDtd[element]);
                        if (action === 'remove') {
                            delete(tmp[child]);
                            qtiDtd[element] = tmp;
                        }
                        // add child element to element as long as it's not entirely unknown to ckeditor
                        else if (action === 'add' && typeof qtiDtd[child] !== 'undefined') {
                            tmp[child] = 1;
                            qtiDtd[element] = tmp[child];
                        }
                    }
                }
            }

            return qtiDtd;
        }());

        return {
            getDtd: getDtd,
            getChildrenOf: getChildrenOf,
            getParentsOf: getParentsOf,
            isChildOf: isChildOf,
            isParentOf: isParentOf,
            getMode: getMode,
            setMode: setMode
        }
    }());

    return dtdHandler;
});
