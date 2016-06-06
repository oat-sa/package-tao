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
    'util/encode',
    'lodash',
    'jquery'
], function(encode, _, $){

    QUnit.module('API');

    var htmlDataProvider = [{
        title : 'HTML content',
        html : '<div class="tao-content">Lorem ipsum <i>dummy</i>i> <strong>blabla</strong> <span id="fragment">text</span> <a href="http://www.tao.com/lorem-ipsum#tatata">with a link</a></div>',
        expected : '&lt;div class="tao-content"&gt;Lorem ipsum &lt;i&gt;dummy&lt;/i&gt;i&gt; &lt;strong&gt;blabla&lt;/strong&gt; &lt;span id="fragment"&gt;text&lt;/span&gt; &lt;a href="http://www.tao.com/lorem-ipsum#tatata"&gt;with a link&lt;/a&gt;&lt;/div&gt;'
    }, {
        title : 'Script content',
        html : '<script type="javascript">alert(\'Hello!\');</script>',
        expected : '&lt;script type="javascript"&gt;alert(\'Hello!\');&lt;/script&gt;'
    }];

    QUnit
        .cases(htmlDataProvider)
        .test('encode HTML ', function(data, assert){
            var result = encode.html(data.html);
            assert.ok(typeof result === 'string', 'The result is a string');
            assert.equal(result, data.expected, 'The result is equal to the expected value');
        });

    var attributeDataProvider = [{
        title : 'HTML content',
        html : '<div class="tao-content">Lorem ipsum <i>dummy</i>i> <strong>blabla</strong> éàü <span id="fragment">text</span> <a href="http://www.tao.com/lorem-ipsum#tatata">with a link</a></div>',
        expected : '&lt;div class=&quot;tao-content&quot;&gt;Lorem ipsum &lt;i&gt;dummy&lt;/i&gt;i&gt; &lt;strong&gt;blabla&lt;/strong&gt; éàü &lt;span id=&quot;fragment&quot;&gt;text&lt;/span&gt; &lt;a href=&quot;http://www.tao.com/lorem-ipsum#tatata&quot;&gt;with a link&lt;/a&gt;&lt;/div&gt;'
    }, {
        title : 'Script content',
        html : '<script type="javascript">alert(\'Hello!\');</script>',
        expected : '&lt;script type=&quot;javascript&quot;&gt;alert(&apos;Hello!&apos;);&lt;/script&gt;'
    }];

    QUnit
        .cases(attributeDataProvider)
        .test('encode Attribute ', function(data, assert){
            var result = encode.attribute(data.html);
            assert.ok(typeof result === 'string', 'The result is a string');
            assert.equal(result, data.expected, 'The result is equal to the expected value');
        });

});


