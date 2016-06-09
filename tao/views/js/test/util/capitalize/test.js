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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define([
    'util/capitalize',
    'lodash'
], function(capitalize, _){

    QUnit.module('API');

    QUnit.test('util api', function(assert){
        assert.equal(capitalize('lorem'), 'Lorem', 'Single word input');
        assert.equal(capitalize('lorem ipsum dolor sit amet'), 'Lorem Ipsum Dolor Sit Amet', 'Multiple words input');
        assert.equal(capitalize('lorem ipsum dolor sit amet', false), 'Lorem ipsum dolor sit amet', 'Multiple words, 2nd arg set to false');
        assert.equal(capitalize('lorem ipsum dolor sit amet', null), 'Lorem Ipsum Dolor Sit Amet', 'multiple words, 2nd arg null (must be bool)');
        assert.equal(capitalize('12.3'), '12.3', 'valid but nonsense arg string "12.3"');
        assert.equal(capitalize('üabc éabc'), 'Üabc Éabc', 'Accents and such');
        assert.equal(capitalize(undefined), undefined, '1st arg must be String but is undefined');
        assert.ok(_.isPlainObject(capitalize({})) && _.isEmpty(capitalize({})), '1st arg must be String but is {}');
    });

});


