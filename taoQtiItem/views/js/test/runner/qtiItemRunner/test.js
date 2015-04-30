define([
    'jquery',
    'lodash',
    'taoQtiItem/runner/qtiItemRunner',
    'json!taoQtiItem/test/samples/json/space-shuttle.json'
], function($, _, qtiItemRunner, itemData){

    var containerId = 'item-container';


    QUnit.module('Runner API');

    QUnit.test('module', function(assert){
        assert.ok(typeof qtiItemRunner !== 'undefined', "The module exports something");
        assert.ok(typeof qtiItemRunner === 'function', "The module exports a function");
    });

    QUnit.module('QTI Provider');

    QUnit.test('provider registered', function(assert){
        assert.ok(typeof qtiItemRunner.providers !== 'undefined', "The runner has providers");
        assert.ok(typeof qtiItemRunner.providers.qti !== 'undefined', "The runner has a QTI provider");
    });

});

