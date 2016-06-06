/*
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
 *
 */
define([
    'lodash',
    'jquery',
    'taoQtiTest/testRunner/testMetaData'
], function (_, $, testMetaDataFactory) {
    'use strict';

    QUnit.test("Constructor", function (assert) {
        var testServiceCallId = "http://sample/first.rdf#i14435993288775133.item-1.0";
        assert.throws(
            function() {
                testMetaDataFactory();
            },
            "testServiceCallId option is required"
        );

        var testMetaData = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });

        assert.equal(testMetaData.getTestServiceCallId(), testServiceCallId);
    });

    QUnit.test("setData", function (assert) {
        var testServiceCallId, testObject1, testObject2;
        testServiceCallId = "http://sample/first.rdf#i14435993288775133.item-2.0";
        testObject1 = {
            param1: 1,
            param2: 2
        };
        testObject2 = {
            param1: 3,
            param2: 4
        };

        var testMetaData = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });

        testMetaData.setData(testObject1);
        //Data should be cloned
        assert.notEqual(testMetaData.getData(), testObject1);
        assert.deepEqual(testMetaData.getData(), testObject1);

        //should not be overwritten
        testMetaData.setData(testObject2);
        assert.deepEqual(testMetaData.getData(), testObject2);
    });

    QUnit.test("addData", function (assert) {
        var testServiceCallId, testObject1, testObject2;
        testServiceCallId = "http://sample/first.rdf#i14435993288775133.item-3.0";
        testObject1 = {
            param1: 1,
            param2: 2
        };
        testObject2 = {
            param1: 3,
            param2: 4
        };

        var testMetaData = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });

        testMetaData.setData(testObject1);

        //should not be overwritten
        testMetaData.addData(testObject2);
        assert.deepEqual(testMetaData.getData(), testObject1);

        //should be overwritten
        testMetaData.addData(testObject2, true);
        assert.deepEqual(testMetaData.getData(), testObject2);
    });


    QUnit.test("clearData", function (assert) {
        var testServiceCallId, testServiceCallId2, testObject1;
        testServiceCallId = "http://sample/first.rdf#i14435993288775133.item-4.0";
        testServiceCallId2 = "http://sample/first.rdf#i14435993288775133.item-5.0";
        testObject1 = {
            param1: 1,
            param2: 2
        };

        var testMetaData = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });

        testMetaData.setData(testObject1);
        assert.deepEqual(testMetaData.getData(), testObject1);


        var testMetaData2 = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });
        //the same testServiceCallId - the same data
        assert.deepEqual(testMetaData.getData(), testMetaData2.getData());

        var testMetaData3 = testMetaDataFactory({
            testServiceCallId : testServiceCallId2
        });
        //different testServiceCallId - different data
        assert.deepEqual(testMetaData3.getData(), {});

        testMetaData.clearData();
        assert.deepEqual(localStorage.getItem(testMetaData.getLocalStorageKey()), null);
        assert.deepEqual(testMetaData.getData(), {});
        assert.notEqual(localStorage.getItem(testMetaData3.getLocalStorageKey()), null);

        testMetaData3.clearData();
        assert.deepEqual(localStorage.getItem(testMetaData3.getLocalStorageKey()), null);

        //new testMetaData object created with cleared testServiceCallId should has no data.
        var testMetaData4 = testMetaDataFactory({
            testServiceCallId : testServiceCallId
        });
        assert.deepEqual(testMetaData4.getData(), {});
    });

});
