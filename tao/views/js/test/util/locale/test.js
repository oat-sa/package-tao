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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Ivan Klimchuk <klimchuk@1pt.com>
 */
define(['module', 'util/locale'], function(module, locale) {

    QUnit.module('API');

    QUnit.test('util api', function(assert) {

        // American style
        locale.setConfig({
            decimalSeparator: '.',
            thousandsSeparator: ','
        });

        assert.equal(locale.getDecimalSeparator(), '.', 'Default decimal separator');
        assert.equal(locale.getThousandsSeparator(), ',', 'Default thousands separator');

        assert.equal(locale.parseFloat('6.000'), 6.0, 'the valid float value with dot as decimal separator');
        assert.equal(locale.parseFloat('6,000'), 6000.0, 'the valid float value with comma as thousands separator');
        assert.equal(locale.parseFloat('6,000.123'), 6000.123, 'the valid float value with dot as decimal separator and comma as thousands separator');

        assert.equal(locale.parseInt('6000'), 6000, 'the valid integer value without separators');
        assert.equal(locale.parseInt('6.000'), 6, 'the valid integer value with dot as decimal separator');
        assert.equal(locale.parseInt('6,000'), 6000, 'the valid integer value with comma as thousands separator');
        assert.equal(locale.parseInt('6,000.123'), 6000, 'the valid integer value with dot as decimal separator and comma as thousands separator');

        // Other style
        locale.setConfig({
            decimalSeparator: ',',
            thousandsSeparator: ''
        });

        assert.equal(locale.parseFloat('6.000'), 6.0, 'the valid float value with dot as decimal separator');
        assert.equal(locale.parseFloat('6,000'), 6.0, 'the valid float value with comma as thousands separator');
        assert.equal(locale.parseFloat('6,000.123'), 6.0, 'the valid float value with dot as decimal separator and comma as thousands separator');

        assert.equal(locale.parseInt('6000'), 6000, 'the valid integer value without separators');
        assert.equal(locale.parseInt('6.000'), 6, 'the valid integer value with dot as decimal separator');
        assert.equal(locale.parseInt('6,000'), 6, 'the valid integer value with comma as thousands separator');
        assert.equal(locale.parseInt('6,000.123'), 6, 'the valid integer value with dot as decimal separator and comma as thousands separator');

    });

});


