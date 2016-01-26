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
define(['lib/history/history'], function(historyLib){
    'use strict';

    var location = window.history.location || window.location;
    var port = location.port;
    var protocol = location.protocol;
    var domain = protocol + '//' + location.hostname;
    var testerUrl = location.href;

    if (port && (('http:' === protocol && '80' !== port) || ('https:' === protocol && '443' !== port))) {
        domain += ':' + port;
    }

    QUnit.module('API');

    QUnit.test('history api', 8, function(assert){
        assert.ok(typeof historyLib === 'object', "The historyLib module exposes an object");
        assert.ok(typeof historyLib.pushState === 'function', "historyLib exposes a pushState method");
        assert.ok(typeof historyLib.replaceState === 'function', "historyLib exposes a replaceState method");
        assert.ok(typeof historyLib.back === 'function', "historyLib exposes a back method");
        assert.ok(typeof historyLib.forward === 'function', "historyLib exposes a forward method");
        assert.ok(typeof historyLib.go === 'function', "historyLib exposes a go method");
        assert.ok(typeof historyLib.state === 'object', "historyLib exposes a state object");
        assert.ok(typeof historyLib.location === 'object', "historyLib exposes a location object");
    });

    QUnit.module('States');

    /** pushState **/
    var pushStatesDataProvider = [{
        state    : { sectionId : 'manage_items', restoreWith : 'activate' },
        title    : 'Manage Items',
        url      : '/Main/index?structure=items&ext=taoItems&section=manage_items',
        expected : {
            state : { sectionId : 'manage_items', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=manage_items'
        }
    }, {
        state    : { sectionId : 'authoring', restoreWith : 'activate' },
        title    : 'Authoring',
        url      : '/Main/index?structure=items&ext=taoItems&section=authoring',
        expected : {
            state : { sectionId : 'authoring', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=authoring'
        }
    }];

    QUnit
        .cases(pushStatesDataProvider)
        .asyncTest('pushState', function(data, assert) {
            historyLib.pushState(data.state, data.title, data.url);
            QUnit.expect(2);

            setTimeout(function(){
                assert.deepEqual(historyLib.state, data.expected.state, 'The current history state must comply to the last pushed state');
                assert.equal(location.href, data.expected.url, 'The current page URL must comply to the target state');
                QUnit.start();
            }, 500);
        });

    /** replaceState **/
    var replaceStatesDataProvider = [{
        state    : { sectionId : 'manage_items', restoreWith : 'activate' },
        title    : 'Manage Items',
        url      : '/Main/index?structure=items&ext=taoItems&section=manage_items',
        expected : {
            state : { sectionId : 'manage_items', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=manage_items'
        }
    }, {
        state    : { sectionId : 'authoring', restoreWith : 'activate' },
        title    : 'Authoring',
        url      : '/Main/index?structure=items&ext=taoItems&section=authoring',
        expected : {
            state : { sectionId : 'authoring', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=authoring'
        }
    }];

    QUnit
        .cases(replaceStatesDataProvider)
        .asyncTest('replaceState', function(data, assert) {
            QUnit.expect(2);

            historyLib.replaceState(data.state, data.title, data.url);

            setTimeout(function(){
                assert.deepEqual(historyLib.state, data.expected.state, 'The current history state must comply to the last replaced state');
                assert.equal(location.href, data.expected.url, 'The current page URL must comply to the target state');
                QUnit.start();
            }, 500);
        });

    QUnit.module('Navigation');

    /** back **/
    var backNavigationDataProvider = [{
        title : 'Step back to Manage Items',
        expected : {
            state : { sectionId : 'manage_items', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=manage_items'
        }
    }, {
        title : 'Step back to Root',
        expected : {
            state : null,
            url   : testerUrl
        }
    }];

    QUnit
        .cases(backNavigationDataProvider)
        .asyncTest('navigation', function(data, assert) {
            QUnit.expect(2);

            historyLib.back();

            setTimeout(function(){
                assert.deepEqual(historyLib.state, data.expected.state, 'The current history state must comply to the right state after stepping back');
                assert.equal(location.href, data.expected.url, 'The current page URL must comply to the target state');
                QUnit.start();
            }, 500);
        });

    /** forward **/
    var forwardNavigationDataProvider = [{
        title : 'Step forward to Manage Items',
        expected : {
            state : { sectionId : 'manage_items', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=manage_items'
        }
    }, {
        title : 'Step forward to Authoring',
        expected : {
            state : { sectionId : 'authoring', restoreWith : 'activate' },
            url   : domain + '/Main/index?structure=items&ext=taoItems&section=authoring'
        }
    }];

    QUnit
        .cases(forwardNavigationDataProvider)
        .asyncTest('navigation', function(data, assert) {
            QUnit.expect(2);

            historyLib.forward();

            setTimeout(function(){
                assert.deepEqual(historyLib.state, data.expected.state, 'The current history state must comply to the right state after stepping forward');
                assert.equal(location.href, data.expected.url, 'The current page URL must comply to the target state');
                QUnit.start();
            }, 500);
        });

    /** restoreContext **/
    QUnit
        .asyncTest('restoreContext', function(assert) {
            QUnit.expect(2);

            historyLib.pushState(null, window.title, testerUrl);

            setTimeout(function(){
                assert.deepEqual(historyLib.state, null, 'The current history state must comply to the last pushed state');
                assert.equal(location.href, testerUrl, 'The current page URL must comply to the target state');
                QUnit.start();
            }, 500);
        });
});
