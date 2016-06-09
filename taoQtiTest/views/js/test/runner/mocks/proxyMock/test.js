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
    'lodash',
    'core/promise',
    'taoQtiTest/test/runner/mocks/proxyMock'
], function (_, Promise, proxyMockFactory) {
    'use strict';

    QUnit.module('proxyMock');

    QUnit.test('module', function (assert) {
        QUnit.expect(3);
        assert.equal(typeof proxyMockFactory, 'function', "The proxyMock module exposes a function");
        assert.equal(typeof proxyMockFactory(), 'object', "The proxyMock factory produces an object");
        assert.notStrictEqual(proxyMockFactory(), proxyMockFactory(), "The proxyMock factory provides a different object on each call");
    });

    var proxyApi = [
        {name: 'init', title: 'init'},
        {name: 'destroy', title: 'destroy'},
        {name: 'getTestData', title: 'getTestData'},
        {name: 'getTestContext', title: 'getTestContext'},
        {name: 'getTestMap', title: 'getTestMap'},
        {name: 'callTestAction', title: 'callTestAction'},
        {name: 'getItem', title: 'getItem'},
        {name: 'submitItem', title: 'submitItem'},
        {name: 'callItemAction', title: 'callItemAction'}
    ];

    QUnit
        .cases(proxyApi)
        .test('instance API ', function (data, assert) {
            var instance = proxyMockFactory();
            QUnit.expect(1);
            assert.equal(typeof instance[data.name], 'function', 'The proxyMock instance exposes a "' + data.name + '" function');
        });


    QUnit.asyncTest('proxyMock.init', function (assert) {
        var initConfig = {
            data: {}
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function (config) {
                    assert.ok(true, 'The mock action init have been called');
                    assert.equal(config, initConfig, 'The mock has provided the config object to the init method');
                    return Promise.resolve(config.data);
                }
            }
        });

        QUnit.expect(10);
        QUnit.stop();

        proxy.init(initConfig)
            .then(function (data) {
                assert.ok(true, 'The init promise is resolved');
                assert.equal(data, initConfig.data, 'The mock has provided the data');
                QUnit.start();
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });

        proxyMockFactory().init()
            .then(function () {
                assert.ok(false, 'The init promise must not be resolved');
                QUnit.start();
            })
            .catch(function (err) {
                assert.ok(true, 'The init promise is rejected');
                assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                assert.equal(err.success, false, 'The promise has provided the error status');
                assert.equal(err.type, 'error', 'The promise has provided the error type');
                assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.destroy', function (assert) {
        var proxy = proxyMockFactory();

        QUnit.expect(1);

        proxy.destroy()
            .then(function (data) {
                assert.ok(true, 'The destroy promise is resolved');
                QUnit.start();
            })
            .catch(function () {
                assert.ok(false, 'The destroy promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.getTestData', function (assert) {
        var testData = {
            data: {}
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                },
                getTestData: function () {
                    return Promise.resolve(testData);
                }
            }
        });

        QUnit.expect(8);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.getTestData()
                    .then(function (data) {
                        assert.ok(true, 'The getTestData promise is resolved');
                        assert.equal(data, testData, 'The mock has provided the data');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The getTestData promise must not be rejected');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });


        var proxy2 = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            }
        });

        proxy2.init()
            .then(function () {
                proxy2.getTestData()
                    .then(function () {
                        assert.ok(false, 'The getTestData promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The getTestData promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.getTestContext', function (assert) {
        var testContext = {
            context: {}
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                },
                getTestContext: function () {
                    return Promise.resolve(testContext);
                }
            }
        });

        QUnit.expect(8);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.getTestContext()
                    .then(function (data) {
                        assert.ok(true, 'The getTestContext promise is resolved');
                        assert.equal(data, testContext, 'The mock has provided the data');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The getTestContext promise must not be rejected');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });


        var proxy2 = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            }
        });

        proxy2.init()
            .then(function () {
                proxy2.getTestContext()
                    .then(function () {
                        assert.ok(false, 'The getTestContext promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The getTestContext promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.getTestMap', function (assert) {
        var testMap = {
            map: {}
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                },
                getTestMap: function () {
                    return Promise.resolve(testMap);
                }
            }
        });

        QUnit.expect(8);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.getTestMap()
                    .then(function (data) {
                        assert.ok(true, 'The getTestMap promise is resolved');
                        assert.equal(data, testMap, 'The mock has provided the data');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The getTestMap promise must not be rejected');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });


        var proxy2 = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            }
        });

        proxy2.init()
            .then(function () {
                proxy2.getTestMap()
                    .then(function () {
                        assert.ok(false, 'The getTestMap promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The getTestMap promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.callTestAction', function (assert) {
        var params = {
            data: {}
        };
        var response = {
            success: true
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                },
                finish: function (actionParams) {
                    assert.equal(actionParams, params, 'The mock has received the params');
                    return Promise.resolve(response);
                }
            }
        });

        QUnit.expect(9);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.callTestAction('finish', params)
                    .then(function (data) {
                        assert.ok(true, 'The callTestAction promise is resolved');
                        assert.equal(data, response, 'The mock has provided the data');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The callTestAction promise must not be rejected');
                        QUnit.start();
                    });

                proxy.callTestAction('unknown')
                    .then(function () {
                        assert.ok(false, 'The callTestAction promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The callTestAction promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.getItem', function (assert) {
        var uri = 'item-0';
        var itemData = {
            data: {}
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            },
            itemActions: {
                getItem: function (actionUri) {
                    assert.equal(actionUri, uri, 'The mock has received the uri');
                    return Promise.resolve(itemData);
                }
            }
        });

        QUnit.expect(11);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.getItem(uri)
                    .then(function (data) {
                        assert.ok(true, 'The getItem promise is resolved');
                        assert.equal(typeof data, 'object', 'The mock has provided the data');
                        assert.equal(data.itemData, itemData, 'The mock has provided the item data');
                        assert.equal(typeof data.itemState, 'object', 'The mock has provided the item state');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The getItem promise must not be rejected');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });


        var proxy2 = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            }
        });

        proxy2.init()
            .then(function () {
                proxy2.getItem(uri)
                    .then(function () {
                        assert.ok(false, 'The getItem promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The getItem promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.submitItem', function (assert) {
        var uri = 'item-0';
        var state = {
            RESPONSE: {}
        };
        var response = {
            RESPONSE: {}
        };
        var itemData = {
            data: {}
        };
        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            },
            itemActions: {
                getItem: function (actionUri) {
                    assert.equal(actionUri, uri, 'The mock has received the uri');
                    return Promise.resolve(itemData);
                }
            }
        });

        QUnit.expect(9);

        proxy.init()
            .then(function () {
                assert.ok(true, 'The init promise is resolved');

                proxy.submitItem(uri, state, response)
                    .then(function (data) {
                        assert.ok(true, 'The submitItem promise is resolved');
                        assert.equal(typeof data, 'object', 'The mock has provided a response');
                        assert.equal(data.success, true, 'The mock has provided a successful response');

                        proxy.getItem(uri)
                            .then(function (data) {
                                assert.ok(true, 'The getItem promise is resolved');
                                assert.equal(typeof data, 'object', 'The mock has provided a response');
                                assert.equal(data.success, true, 'The mock has provided a successful response');
                                assert.equal(data.itemState, state, 'The mock has provided the data');
                                QUnit.start();
                            })
                            .catch(function () {
                                assert.ok(false, 'The getItem promise must not be rejected');
                                QUnit.start();
                            });
                    })
                    .catch(function () {
                        assert.ok(false, 'The submitItem promise must not be rejected');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });


    QUnit.asyncTest('proxyMock.callItemAction', function (assert) {
        var uri = 'item-0';
        var params = {
            data: {}
        };
        var response = {
            success: true
        };

        var proxy = proxyMockFactory({
            testActions: {
                init: function () {
                    return Promise.resolve();
                }
            },
            itemActions: {
                move: function (actionUri, actionParams) {
                    assert.equal(actionUri, uri, 'The mock has received the uri');
                    assert.equal(actionParams, params, 'The mock has received the params');
                    return Promise.resolve(response);
                }
            }
        });

        QUnit.expect(10);
        QUnit.stop();

        proxy.init()
            .then(function () {
                proxy.callItemAction(uri, 'move', params)
                    .then(function (data) {
                        assert.ok(true, 'The callItemAction promise is resolved');
                        assert.equal(data, response, 'The mock has provided the data');
                        QUnit.start();
                    })
                    .catch(function () {
                        assert.ok(false, 'The callItemAction promise must not be rejected');
                        QUnit.start();
                    });

                proxy.callItemAction(uri, 'unknown')
                    .then(function () {
                        assert.ok(false, 'The callItemAction promise must not be resolved');
                        QUnit.start();
                    })
                    .catch(function (err) {
                        assert.ok(true, 'The callItemAction promise is rejected');
                        assert.equal(typeof err, 'object', 'The promise has provided the error descriptor');
                        assert.equal(err.success, false, 'The promise has provided the error status');
                        assert.equal(err.type, 'error', 'The promise has provided the error type');
                        assert.equal(typeof err.code, 'number', 'The promise has provided the error code');
                        assert.equal(typeof err.message, 'string', 'The promise has provided the error message');
                        QUnit.start();
                    });
            })
            .catch(function () {
                assert.ok(false, 'The init promise must not be rejected');
                QUnit.start();
            });
    });
});
