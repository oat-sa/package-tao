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
    'helpers',
    'taoQtiTest/runner/helpers/map',
    'json!taoQtiTest/test/runner/helpers/map/map.json'
], function(_, helpers, mapHelper, mapSample) {
    'use strict';

    QUnit.module('helpers/map');


    QUnit.test('module', 1, function(assert) {
        assert.equal(typeof mapHelper, 'object', "The map helper module exposes an object");
    });


    var mapHelperApi = [
        { name : 'getJumps', title : 'getJumps' },
        { name : 'getParts', title : 'getParts' },
        { name : 'getJump', title : 'getJump' },
        { name : 'getPart', title : 'getPart' },
        { name : 'getSection', title : 'getSection' },
        { name : 'getItem', title : 'getItem' },
        { name : 'getTestStats', title : 'getTestStats' },
        { name : 'getPartStats', title : 'getPartStats' },
        { name : 'getSectionStats', title : 'getSectionStats' },
        { name : 'getScopeStats', title : 'getScopeStats' },
        { name : 'getItemPart', title : 'getItemPart' },
        { name : 'getItemSection', title : 'getItemSection' },
        { name : 'getItemAt', title : 'getItemAt' },
        { name : 'updateItemStats', title : 'updateItemStats' },
        { name : 'computeItemStats', title : 'computeItemStats' },
        { name : 'computeStats', title : 'computeStats' }
    ];

    QUnit
        .cases(mapHelperApi)
        .test('helpers/map API ', 1, function(data, assert) {
            assert.equal(typeof mapHelper[data.name], 'function', 'The map helper expose a "' + data.name + '" function');
        });


    QUnit.test('helpers/map.getJumps', 3, function(assert) {
        var map = {
            jumps : []
        };

        assert.equal(mapHelper.getJumps(map), map.jumps, 'The map helper getJumps provides the map jumps');
        assert.equal(mapHelper.getJumps({}), undefined, 'The map helper getJumps does not provide the map jumps when the map is wrong');
        assert.equal(mapHelper.getJumps(), undefined, 'The map helper getJumps does not provide the map jumps when the map does not exist');
    });


    QUnit.test('helpers/map.getParts', 3, function(assert) {
        var map = {
            parts : {}
        };

        assert.equal(mapHelper.getParts(map), map.parts, 'The map helper getParts provides the map parts');
        assert.equal(mapHelper.getParts({}), undefined, 'The map helper getParts does not provide the map parts when the map is wrong');
        assert.equal(mapHelper.getParts(), undefined, 'The map helper getParts does not provide the map parts when the map does not exist');
    });


    QUnit.test('helpers/map.getJump', 4, function(assert) {
        var map = {
            jumps : [
                {identifier: "item-1"},
                {identifier: "item-2"}
            ]
        };

        assert.equal(mapHelper.getJump(map, 1), map.jumps[1], 'The map helper getJump provides the right jump');
        assert.equal(mapHelper.getJump(map, 10), undefined, 'The map helper getJump does not provide any jump when the position does not exist');
        assert.equal(mapHelper.getJump({}), undefined, 'The map helper getJump does not provide any jump when the map is wrong');
        assert.equal(mapHelper.getJump(), undefined, 'The map helper getJump does not provide any jump when the map does not exist');
    });


    QUnit.test('helpers/map.getPart', 4, function(assert) {
        assert.equal(mapHelper.getPart(mapSample, 'testPart-2'), mapSample.parts['testPart-2'], 'The map helper getPart provides the right part');
        assert.equal(mapHelper.getPart(mapSample, 'testPart-0'), undefined, 'The map helper getPart does not provide any part when the part does not exist');
        assert.equal(mapHelper.getPart({}), undefined, 'The map helper getPart does not provide any part when the map is wrong');
        assert.equal(mapHelper.getPart(), undefined, 'The map helper getPart does not provide any part when the map does not exist');
    });

    
    QUnit.test('helpers/map.getSection', 4, function(assert) {
        assert.equal(mapHelper.getSection(mapSample, 'assessmentSection-3'), mapSample.parts['testPart-2'].sections['assessmentSection-3'], 'The map helper getSection provides the right section');
        assert.equal(mapHelper.getSection(mapSample, 'assessmentSection-0'), undefined, 'The map helper getSection does not provide any section when the section does not exist');
        assert.equal(mapHelper.getSection({}), undefined, 'The map helper getSection does not provide any section when the map is wrong');
        assert.equal(mapHelper.getSection(), undefined, 'The map helper getSection does not provide any section when the map does not exist');
    });
    
    
    QUnit.test('helpers/map.getItem', 4, function(assert) {
        assert.equal(mapHelper.getItem(mapSample, 'item-5'), mapSample.parts['testPart-1'].sections['assessmentSection-2'].items['item-5'], 'The map helper getItem provides the right item');
        assert.equal(mapHelper.getItem(mapSample, 'item-0'), undefined, 'The map helper getItem does not provide any item when the item does not exist');
        assert.equal(mapHelper.getItem({}), undefined, 'The map helper getItem does not provide any item when the map is wrong');
        assert.equal(mapHelper.getItem(), undefined, 'The map helper getItem does not provide any item when the map does not exist');
    });


    QUnit.test('helpers/map.getTestStats', 3, function(assert) {
        var map = {
            stats: {}
        };

        assert.equal(mapHelper.getTestStats(map), map.stats, 'The map helper getTestStats provides the right stats');
        assert.equal(mapHelper.getTestStats({}), undefined, 'The map helper getTestStats does not provide any stats when the map is wrong');
        assert.equal(mapHelper.getTestStats(), undefined, 'The map helper getTestStats does not provide any stats when the map does not exist');
    });


    QUnit.test('helpers/map.getPartStats', 4, function(assert) {
        assert.equal(mapHelper.getPartStats(mapSample, 'testPart-2'), mapSample.parts['testPart-2'].stats, 'The map helper getPartStats provides the right stats');
        assert.equal(mapHelper.getPartStats(mapSample, 'testPart-0'), undefined, 'The map helper getPartStats does not provide any stats when the part does not exist');
        assert.equal(mapHelper.getPartStats({}), undefined, 'The map helper getPartStats does not provide any stats when the map is wrong');
        assert.equal(mapHelper.getPartStats(), undefined, 'The map helper getPartStats does not provide any stats when the map does not exist');
    });
    
    
    QUnit.test('helpers/map.getSectionStats', 4, function(assert) {
        assert.equal(mapHelper.getSectionStats(mapSample, 'assessmentSection-3'), mapSample.parts['testPart-2'].sections['assessmentSection-3'].stats, 'The map helper getSectionStats provides the right stats');
        assert.equal(mapHelper.getSectionStats(mapSample, 'assessmentSection-0'), undefined, 'The map helper getSectionStats does not provide any stats when the section does not exist');
        assert.equal(mapHelper.getSectionStats({}), undefined, 'The map helper getSectionStats does not provide any stats when the map is wrong');
        assert.equal(mapHelper.getSectionStats(), undefined, 'The map helper getSectionStats does not provide any stats when the map does not exist');
    });
    
    
    QUnit.test('helpers/map.getScopeStats', 9, function(assert) {
        assert.equal(mapHelper.getScopeStats(mapSample, 6), mapHelper.getScopeStats(mapSample, 6, 'test'), 'The map helper getScopeStats use the "test" scope by default');
        assert.equal(mapHelper.getScopeStats(mapSample, 6, 'test'), mapSample.stats, 'The map helper getScopeStats provides the right stats when the scope is "test"');
        assert.equal(mapHelper.getScopeStats(mapSample, 6, 'part'), mapSample.parts['testPart-2'].stats, 'The map helper getScopeStats provides the right stats when the scope is "part"');
        assert.equal(mapHelper.getScopeStats(mapSample, 6, 'section'), mapSample.parts['testPart-2'].sections['assessmentSection-3'].stats, 'The map helper getScopeStats provides the right stats when the scope is "section');
        assert.equal(mapHelper.getScopeStats(mapSample, 100, 'test'), mapSample.stats, 'The map helper getScopeStats still provide any stats when the position does not exist but the scope is "test"');
        assert.equal(mapHelper.getScopeStats(mapSample, 100, 'part'), undefined, 'The map helper getScopeStats does not provide any stats when the position does not exist and the scope is "part"');
        assert.equal(mapHelper.getScopeStats(mapSample, 100, 'section'), undefined, 'The map helper getScopeStats does not provide any stats when the section does not exist and the scope is "section"');
        assert.equal(mapHelper.getScopeStats({}, 1), undefined, 'The map helper getScopeStats does not provide any stats when the map is wrong');
        assert.equal(mapHelper.getScopeStats(), undefined, 'The map helper getScopeStats does not provide any stats when the map does not exist');
    });
    
    
    QUnit.test('helpers/map.getItemPart', 4, function(assert) {
        assert.equal(mapHelper.getItemPart(mapSample, 8), mapSample.parts['testPart-2'], 'The map helper getItemPart provides the right part');
        assert.equal(mapHelper.getItemPart(mapSample, 100), undefined, 'The map helper getItemPart does not provide any part when the position does not exist');
        assert.equal(mapHelper.getItemPart({}), undefined, 'The map helper getItemPart does not provide any part when the map is wrong');
        assert.equal(mapHelper.getItemPart(), undefined, 'The map helper getItemPart does not provide any part when the map does not exist');
    });


    QUnit.test('helpers/map.getItemSection', 4, function(assert) {
        assert.equal(mapHelper.getItemSection(mapSample, 8), mapSample.parts['testPart-2'].sections['assessmentSection-4'], 'The map helper getItemSection provides the right section');
        assert.equal(mapHelper.getItemSection(mapSample, 100), undefined, 'The map helper getItemSection does not provide any section when the position does not exist');
        assert.equal(mapHelper.getItemSection({}), undefined, 'The map helper getItemSection does not provide any section when the map is wrong');
        assert.equal(mapHelper.getItemSection(), undefined, 'The map helper getItemSection does not provide any section when the map does not exist');
    });


    QUnit.test('helpers/map.getItemAt', 4, function(assert) {
        assert.equal(mapHelper.getItemAt(mapSample, 8), mapSample.parts['testPart-2'].sections['assessmentSection-4'].items['item-9'], 'The map helper getItemAt provides the right item');
        assert.equal(mapHelper.getItemAt(mapSample, 100), undefined, 'The map helper getItemAt does not provide any item when the position does not exist');
        assert.equal(mapHelper.getItemAt({}), undefined, 'The map helper getItemAt does not provide any item when the map is wrong');
        assert.equal(mapHelper.getItemAt(), undefined, 'The map helper getItemAt does not provide any item when the map does not exist');
    });


    QUnit.test('helpers/map.updateItemStats', 25, function(assert) {
        var map = _.cloneDeep(mapSample);
        var item = mapHelper.getItemAt(map, 8);
        var section = mapHelper.getItemSection(map, 8);
        var part = mapHelper.getItemPart(map, 8);
        var stats = mapHelper.getTestStats(map);

        assert.equal(item.answered, false, 'The item is not answered at this time');
        assert.equal(item.flagged, false, 'The item is not flagged at this time');
        assert.equal(item.viewed, false, 'The item is not viewed at this time');

        assert.equal(stats.answered, 0, 'There is no answered item at this time in the test');
        assert.equal(stats.flagged, 0, 'There is no flagged item at this time in the test');
        assert.equal(stats.viewed, 1, 'There is one viewed item at this time in the test');

        assert.equal(part.stats.answered, 0, 'There is no answered item at this time in the part');
        assert.equal(part.stats.flagged, 0, 'There is no flagged item at this time in the part');
        assert.equal(part.stats.viewed, 0, 'There is no viewed item at this time in the part');

        assert.equal(section.stats.answered, 0, 'There is no answered item at this time in the section');
        assert.equal(section.stats.flagged, 0, 'There is no flagged item at this time in the section');
        assert.equal(section.stats.viewed, 0, 'There is no viewed item at this time in the section');

        item.answered = true;
        item.flagged = true;
        item.viewed = true;

        assert.equal(mapHelper.updateItemStats(map, 8), map, 'The map helper updateItemStats returns the map');

        assert.equal(item.answered, true, 'The item is now answered');
        assert.equal(item.flagged, true, 'The item is now flagged');
        assert.equal(item.viewed, true, 'The item is now viewed');

        stats = mapHelper.getTestStats(map);
        assert.equal(stats.answered, 1, 'There is one answered item at this time in the test');
        assert.equal(stats.flagged, 1, 'There is one flagged item at this time in the test');
        assert.equal(stats.viewed, 2, 'There is two viewed items at this time in the test');

        assert.equal(part.stats.answered, 1, 'There is one answered item at this time in the part');
        assert.equal(part.stats.flagged, 1, 'There is one flagged item at this time in the part');
        assert.equal(part.stats.viewed, 1, 'There is one viewed item at this time in the part');

        assert.equal(section.stats.answered, 1, 'There is one answered item at this time in the section');
        assert.equal(section.stats.flagged, 1, 'There is one flagged item at this time in the section');
        assert.equal(section.stats.viewed, 1, 'There is one viewed item at this time in the section');
    });


    QUnit.test('helpers/map.computeItemStats', 13, function(assert) {
        var item = mapHelper.getItemAt(mapSample, 6);
        var section = mapHelper.getItemSection(mapSample, 6);
        var stats;

        assert.equal(item.answered, false, 'The item is not answered at this time');
        assert.equal(item.flagged, false, 'The item is not flagged at this time');
        assert.equal(item.viewed, false, 'The item is not viewed at this time');

        assert.equal(section.stats.answered, 0, 'There is no answered item at this time in the section');
        assert.equal(section.stats.flagged, 0, 'There is no flagged item at this time in the section');
        assert.equal(section.stats.viewed, 0, 'There is no viewed item at this time in the section');

        item.answered = true;
        item.flagged = true;
        item.viewed = true;

        stats = mapHelper.computeItemStats(section.items);

        assert.equal(typeof stats, 'object', 'The map helper computeItemStats returns an object');

        assert.equal(item.answered, true, 'The item is now answered');
        assert.equal(item.flagged, true, 'The item is now flagged');
        assert.equal(item.viewed, true, 'The item is now viewed');

        assert.equal(stats.answered, 1, 'There is one answered item at this time in the computed stats');
        assert.equal(stats.flagged, 1, 'There is one flagged item at this time in the computed stats');
        assert.equal(stats.viewed, 1, 'There is one viewed item at this time in the computed stats');
    });


    QUnit.test('helpers/map.computeStats', 20, function(assert) {
        var map = _.cloneDeep(mapSample);
        var section = mapHelper.getItemSection(map, 8);
        var part = mapHelper.getItemPart(map, 8);
        var stats = mapHelper.getTestStats(map);

        assert.equal(stats.answered, 0, 'There is no answered item at this time in the test');
        assert.equal(stats.flagged, 0, 'There is no flagged item at this time in the test');
        assert.equal(stats.viewed, 1, 'There is one viewed item at this time in the test');

        assert.equal(part.stats.answered, 0, 'There is no answered item at this time in the part');
        assert.equal(part.stats.flagged, 0, 'There is no flagged item at this time in the part');
        assert.equal(part.stats.viewed, 0, 'There is no viewed item at this time in the part');

        assert.equal(section.stats.answered, 0, 'There is no answered item at this time in the section');
        assert.equal(section.stats.flagged, 0, 'There is no flagged item at this time in the section');
        assert.equal(section.stats.viewed, 0, 'There is no viewed item at this time in the section');

        section.stats.answered = 2;
        section.stats.flagged = 2;
        section.stats.viewed = 2;

        part.stats = mapHelper.computeStats(part.sections);
        assert.equal(typeof part.stats, 'object', 'The map helper computeStats returns an object');

        stats = mapHelper.computeStats(mapHelper.getParts(map));
        assert.equal(typeof map.stats, 'object', 'The map helper computeStats returns an object');

        assert.equal(stats.answered, 2, 'There is two answered items at this time in the test');
        assert.equal(stats.flagged, 2, 'There is two flagged items at this time in the test');
        assert.equal(stats.viewed, 3, 'There is three viewed items at this time in the test');

        assert.equal(part.stats.answered, 2, 'There is two answered items at this time in the part');
        assert.equal(part.stats.flagged, 2, 'There is two flagged items at this time in the part');
        assert.equal(part.stats.viewed, 2, 'There is two viewed items at this time in the part');

        assert.equal(section.stats.answered, 2, 'There is two answered items at this time in the section');
        assert.equal(section.stats.flagged, 2, 'There is two flagged items at this time in the section');
        assert.equal(section.stats.viewed, 2, 'There is two viewed items at this time in the section');
    });
});
