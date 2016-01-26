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
define(['jquery', 'ui/autocomplete', 'lib/simulator/jquery.keystroker'], function($, autocompleteUI, keystroker) {

    'use strict';

    /**
     * Checks the API
     */

    QUnit.module('API');

    QUnit.test('ui/autocomplete module', 3, function(assert) {
        assert.ok(typeof autocompleteUI === 'function', "The ui/autocomplete module exposes a function");
        assert.ok(typeof autocompleteUI() === 'object', "The ui/autocomplete factory produces an object");
        assert.ok(autocompleteUI() !== autocompleteUI(), "The ui/autocomplete factory provides a different object on each call");
    });

    var autocompleteApi = [
        { name : 'init', title : 'init' },
        { name : 'destroy', title : 'destroy' },
        { name : 'setOptions', title : 'setOptions' },
        { name : 'trigger', title : 'trigger' },
        { name : 'on', title : 'on' },
        { name : 'off', title : 'off' },
        { name : 'getElement', title : 'getElement' },
        { name : 'getValue', title : 'getValue' },
        { name : 'setValue', title : 'setValue' },
        { name : 'getLabel', title : 'getLabel' },
        { name : 'setLabel', title : 'setLabel' },
        { name : 'getOntology', title : 'getOntology' },
        { name : 'setOntology', title : 'setOntology' },
        { name : 'getValueField', title : 'getValueField' },
        { name : 'setValueField', title : 'setValueField' },
        { name : 'getLabelField', title : 'getLabelField' },
        { name : 'setLabelField', title : 'setLabelField' },
        { name : 'getIsProvider', title : 'getIsProvider' },
        { name : 'setIsProvider', title : 'setIsProvider' },
        { name : 'getParamsRoot', title : 'getParamsRoot' },
        { name : 'setParamsRoot', title : 'setParamsRoot' },
        { name : 'getParams', title : 'getParams' },
        { name : 'setParams', title : 'setParams' },
        { name : 'getQueryParam', title : 'getQueryParam' },
        { name : 'setQueryParam', title : 'setQueryParam' },
        { name : 'getOntologyParam', title : 'getOntologyParam' },
        { name : 'setOntologyParam', title : 'setOntologyParam' },
        { name : 'getUrl', title : 'getUrl' },
        { name : 'setUrl', title : 'setUrl' },
        { name : 'getType', title : 'getType' },
        { name : 'setType', title : 'setType' },
        { name : 'getDelay', title : 'getDelay' },
        { name : 'setDelay', title : 'setDelay' },
        { name : 'getMinChars', title : 'getMinChars' },
        { name : 'setMinChars', title : 'setMinChars' },
        { name : 'enable', title : 'enable' },
        { name : 'disable', title : 'disable' },
        { name : 'hide', title : 'hide' },
        { name : 'clear', title : 'clear' },
        { name : 'clearCache', title : 'clearCache' },
        { name : 'reset', title : 'reset' }
    ];

    QUnit
        .cases(autocompleteApi)
        .test('ui/autocomplete instance API ', function(data, assert) {
            var instance = autocompleteUI();
            assert.ok(typeof instance[data.name] === 'function', 'The ui/autocomplete instance exposes a "' + data.title + '" function');
        });

    QUnit.test('ui/autocomplete getter/setter', function(assert) {
        var instance = autocompleteUI();
        var expected;

        expected = '12';
        instance.setValue(expected);
        assert.equal(instance.getValue(), expected, 'The ui/autocomplete instance must provide bidirectional access to "value"');

        expected = 'Twelve';
        instance.setLabel(expected);
        assert.equal(instance.getLabel(), expected, 'The ui/autocomplete instance must provide bidirectional access to "label"');

        expected = 'http://www.tao.lu/Ontologies/TAO.rdf#User';
        instance.setOntology(expected);
        assert.equal(instance.getOntology(), expected, 'The ui/autocomplete instance must provide bidirectional access to "ontology"');

        expected = 'value1234';
        instance.setValueField(expected);
        assert.equal(instance.getValueField(), expected, 'The ui/autocomplete instance must provide bidirectional access to "valueField"');

        expected = 'label1234';
        instance.setLabelField(expected);
        assert.equal(instance.getLabelField(), expected, 'The ui/autocomplete instance must provide bidirectional access to "labelField"');

        expected = true;
        instance.setIsProvider(expected);
        assert.equal(instance.getIsProvider(), expected, 'The ui/autocomplete instance must provide bidirectional access to "isProvider"');
        expected = false;
        instance.setIsProvider(expected);
        assert.equal(instance.getIsProvider(), expected, 'The ui/autocomplete instance must provide bidirectional access to "isProvider"');

        expected = 'fragment';
        instance.setQueryParam(expected);
        assert.equal(instance.getQueryParam(), expected, 'The ui/autocomplete instance must provide bidirectional access to "queryParam"');

        expected = 'ontology';
        instance.setOntologyParam(expected);
        assert.equal(instance.getOntologyParam(), expected, 'The ui/autocomplete instance must provide bidirectional access to "ontologyParam"');

        expected = 'params';
        instance.setParamsRoot(expected);
        assert.equal(instance.getParamsRoot(), expected, 'The ui/autocomplete instance must provide bidirectional access to "paramsRoot"');
        assert.equal(instance.getQueryParam(), 'params[fragment]', 'The ui/autocomplete instance must provide adjusted value for "queryParam" when "paramsRoot" is defined');
        assert.equal(instance.getOntologyParam(), 'params[ontology]', 'The ui/autocomplete instance must provide adjusted value for "ontologyParam" when "paramsRoot" is defined');

        expected = 'http://tao.dev/tao/Search/search';
        instance.setUrl(expected);
        assert.equal(instance.getUrl(), expected, 'The ui/autocomplete instance must provide bidirectional access to "url"');

        expected = 'POST';
        instance.setType(expected);
        assert.equal(instance.getType(), expected, 'The ui/autocomplete instance must provide bidirectional access to "type"');

        expected = 'GET';
        instance.setType(null);
        assert.equal(instance.getType(), expected, 'The ui/autocomplete instance must provide default value for "type"');

        expected = 100;
        instance.setDelay(expected);
        assert.equal(instance.getDelay(), expected, 'The ui/autocomplete instance must provide bidirectional access to "delay"');

        expected = 0;
        instance.setDelay(null);
        assert.equal(instance.getDelay(), expected, 'The ui/autocomplete instance must provide default value for "delay"');
        instance.setDelay(-1);
        assert.equal(instance.getDelay(), expected, 'The ui/autocomplete instance must provide default value for "delay"');

        expected = 4;
        instance.setMinChars(expected);
        assert.equal(instance.getMinChars(), expected, 'The ui/autocomplete instance must provide bidirectional access to "minChars"');

        expected = 1;
        instance.setMinChars(null);
        assert.equal(instance.getMinChars(), expected, 'The ui/autocomplete instance must provide default value for "minChars"');
        instance.setMinChars(-1);
        assert.equal(instance.getMinChars(), expected, 'The ui/autocomplete instance must provide default value for "minChars"');

        expected = {
            params: {
                ontology: 'http://www.tao.lu/Ontologies/TAO.rdf#User'
            }
        };
        assert.deepEqual(instance.getParams(), expected, 'The ui/autocomplete instance must provide nested "params" when "paramsRoot" is defined');

        expected = {
            ontology: 'http://www.tao.lu/Ontologies/TAO.rdf#User'
        };
        instance.setParamsRoot(null);
        assert.deepEqual(instance.getParams(), expected, 'The ui/autocomplete instance must provide "params" when "paramsRoot" is not defined');

        expected = {
            ontology: 'http://www.tao.lu/Ontologies/TAO.rdf#User',
            page: 1,
            count: 2
        };
        instance.setParams({
            page: 1,
            count: 2
        });
        assert.deepEqual(instance.getParams(), expected, 'The ui/autocomplete instance must provide bidirectional access to "params"');
    });

    QUnit.test('ui/autocomplete load options from DOM', function(assert) {
        var expectedUrl = 'js/test/ui/autocomplete/test.success.json';
        var expectedOntology = 'http://www.tao.lu/Ontologies/TAO.rdf#User';
        var expectedParamsRoot = 'params';
        var $element = $('<input type="text"' +
                            ' data-url="' + expectedUrl + '"' +
                            ' data-ontology="' + expectedOntology + '"' +
                            ' data-params-root="' + expectedParamsRoot + '" />');

        var instance = autocompleteUI($element);

        assert.equal(instance.getUrl(), expectedUrl, 'The ui/autocomplete instance must provide the value loaded from the DOM for the property "url"');
        assert.equal(instance.getOntology(), expectedOntology, 'The ui/autocomplete instance must provide the value loaded from the DOM for the property "ontology"');
        assert.equal(instance.getParamsRoot(), expectedParamsRoot, 'The ui/autocomplete instance must provide the value loaded from the DOM for the property "paramsRoot"');
    });

    /**
     * Checks the behavior
     */

    QUnit.module('Behavior');

    QUnit.asyncTest('ui/autocomplete install', function(assert) {
        var instance = autocompleteUI('#autocomplete1');
        var element = instance.getElement();
        var stopper;
        var listenerInstalled;
        var eventListener = function() {
            if (listenerInstalled) {
                assert.ok(true, 'The ui/autocomplete instance can handle custom events');
            } else {
                assert.ok(false, 'The ui/autocomplete instance must be able to remove custom events');
            }

            clearTimeout(stopper);
            QUnit.start();
        };

        // basic
        assert.ok(typeof instance === 'object', "An installed autocomplete relies on an object");
        assert.ok(!!element && element.length === 1, "The ui/autocomplete instance relies on a nested element");

        // add custom event
        stopper = setTimeout(function() {
            assert.ok(false, 'The ui/autocomplete instance fails to handle custom events');
            QUnit.start();
        }, 250);

        instance.on('test', eventListener);
        listenerInstalled = true;
        instance.trigger('test');

        // remove custom event
        QUnit.stop();
        stopper = setTimeout(function() {
            assert.ok(true, 'The ui/autocomplete instance can remove custom events');
            QUnit.start();
        }, 250);
        instance.off('test');
        listenerInstalled = false;
        instance.trigger('test');

        // remove the component
        instance.destroy();
    });

    QUnit.asyncTest('ui/autocomplete successful query', function(assert) {
        var instance = autocompleteUI('#autocomplete2', {
            url : 'js/test/ui/autocomplete/test.success.json',

            onSearchStart : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchStart');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchSearch event when the user inputs a query');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(false, 'The ui/autocomplete instance fails to handle searchComplete event');
                    QUnit.start();
                }, 500);
            },

            onSearchComplete : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchComplete');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchComplete event after server response');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(false, 'The ui/autocomplete instance fails to handle selectItem event');
                    QUnit.start();
                }, 500);
                setTimeout(function() {
                    keystroker.keystroke(element, keystroker.keyCode.ENTER);
                }, 250);
            },

            onSelectItem : function() {
                // avoid TU to be broken by multi events calls
                instance.off('selectItem');

                assert.ok(true, 'The ui/autocomplete instance must fire the selectItem event when an item is selected');

                assert.equal(element.val(), "user", 'The ui/autocomplete instance keep the value of the selected item in the textbox');
                assert.equal(instance.getValue(), "http://tao.dev/tao-dev.rdf#i1431522022337107", 'The ui/autocomplete instance must keep the value of the selected item');
                assert.equal(instance.getLabel(), "user", 'The ui/autocomplete instance must keep the label of the selected item');

                clearTimeout(stopper);
                QUnit.start();
            }
        });
        var element = instance.getElement();
        var stopper = setTimeout(function() {
            assert.ok(false, 'The ui/autocomplete instance fails to handle searchStart event');
            QUnit.start();
        }, 500);

        keystroker.puts(element, "user");
    });

    QUnit.asyncTest('ui/autocomplete failed query', function(assert) {
        var instance = autocompleteUI('#autocomplete3', {
            url : 'js/test/ui/autocomplete/test.blank.json',

            onSearchStart : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchStart');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchSearch event when the user inputs a query');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(false, 'The ui/autocomplete instance fails to handle searchComplete event');
                    QUnit.start();
                }, 500);
            },

            onSearchComplete : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchComplete');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchComplete event after server response');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(true, 'The ui/autocomplete instance must not fire the selectItem event when no item is selectable');
                    QUnit.start();
                }, 500);

                setTimeout(function() {
                    keystroker.keystroke(element, keystroker.keyCode.ENTER);
                }, 250);
            },

            onSelectItem : function() {
                // avoid TU to be broken by multi events calls
                instance.off('selectItem');

                assert.ok(false, 'The ui/autocomplete instance must not fire the selectItem event when no item is selectable');

                clearTimeout(stopper);
                QUnit.start();
            }
        });
        var element = instance.getElement();
        var stopper = setTimeout(function() {
            assert.ok(false, 'The ui/autocomplete instance fails to handle searchStart event');
            QUnit.start();
        }, 500);

        keystroker.puts(element, "test");
    });

    QUnit.asyncTest('ui/autocomplete as provider', function(assert) {
        var instance = autocompleteUI('#autocomplete4', {
            url : 'js/test/ui/autocomplete/test.success.json',
            isProvider : true,

            onSearchStart : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchStart');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchSearch event when the user inputs a query');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(false, 'The ui/autocomplete instance fails to handle searchComplete event');
                    QUnit.start();
                }, 500);
            },

            onSearchComplete : function() {
                // avoid TU to be broken by multi events calls
                instance.off('searchComplete');

                assert.ok(true, 'The ui/autocomplete instance must fire the searchComplete event after server response');

                clearTimeout(stopper);
                stopper = setTimeout(function() {
                    assert.ok(false, 'The ui/autocomplete instance fails to handle selectItem event');
                    QUnit.start();
                }, 500);
                setTimeout(function() {
                    keystroker.keystroke(element, keystroker.keyCode.ENTER);
                }, 250);
            },

            onSelectItem : function() {
                // avoid TU to be broken by multi events calls
                instance.off('selectItem');

                assert.ok(true, 'The ui/autocomplete instance must fire the selectItem event when an item is selected');

                assert.equal(element.val(), '', 'The ui/autocomplete instance must clear the value of the selected item in the textbox');
                assert.equal(instance.getValue(), "http://tao.dev/tao-dev.rdf#i1431522022337107", 'The ui/autocomplete instance must keep the value of the selected item');
                assert.equal(instance.getLabel(), "user", 'The ui/autocomplete instance must keep the label of the selected item');

                clearTimeout(stopper);
                QUnit.start();
            }
        });
        var element = instance.getElement();
        var stopper = setTimeout(function() {
            assert.ok(false, 'The ui/autocomplete instance fails to handle searchStart event');
            QUnit.start();
        }, 500);

        keystroker.puts(element, "user");
    });

});
