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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
define(['urlParser'], function(UrlParser){
   
   var url = "http://example.com:3000/extension/module/action?p1=v1&p2=v2#hash";
   var url2 = "https://example.com/extension/module/action.php?a=1";
   var url3 = "https://example.com/extension/module/action?a=1";
   var url4 = "https://example.com?p=a&c=b";
   
    test('parser structure', function(){
        expect(4);
        
        ok(typeof UrlParser === 'function');
        ok(typeof UrlParser.prototype.get === 'function');
        ok(typeof UrlParser.prototype.getPaths === 'function');
        ok(typeof UrlParser.prototype.getParams === 'function');
    });
    
    test('parsing', function(){
        expect(2);
        
        
        var parser = new UrlParser(url);
        equal(parser.url, url);
        deepEqual(parser.data, {
            hash: "#hash",
            host: "example.com:3000",
            hostname: "example.com",
            pathname: "/extension/module/action",
            port: "3000",
            protocol: "http:",
            search: "?p1=v1&p2=v2"
        });
    });
    
    test('get parts', function(){
        expect(7);
        
        var parser = new UrlParser(url);
        equal(parser.get('hash'), "#hash");
        equal(parser.get('host'), "example.com:3000");
        equal(parser.get('hostname'), "example.com");
        equal(parser.get('pathname'), "/extension/module/action");
        equal(parser.get('port'), "3000");
        equal(parser.get('protocol'), "http:");
        equal(parser.get('search'), "?p1=v1&p2=v2");
    });
    
    test('get params', function(){
        expect(1);
        
        var parser = new UrlParser(url);
        deepEqual(parser.getParams(), {
            'p1': 'v1',
            'p2': 'v2'
        });
    });
    
    test('get paths', function(){
        expect(1);
        
        var parser = new UrlParser(url);
        deepEqual(parser.getPaths(), [
            'extension',
            'module',
            'action'
        ]);
    });
    
    test('getUrl', function(){
        expect(4);
        
        equal(new UrlParser(url).getUrl(), url);
        equal(new UrlParser(url2).getUrl(), url2);
        equal(new UrlParser(url3).getUrl(), url3);
        equal(new UrlParser(url4).getUrl(), "https://example.com/?p=a&c=b"); //slash is added
    });
    
    test('getShortUrl', function(){
        expect(2);

        var parser = new UrlParser(url);
        equal(parser.getUrl(['host', 'params', 'hash']), '/extension/module/action');
        equal(parser.getUrl(['params', 'hash']), 'http://example.com:3000/extension/module/action');
    });
    
     test('getBaseUrl', function(){
        expect(1);

        var parser = new UrlParser(url2);
        equal(parser.getBaseUrl(), 'https://example.com/extension/module/');
    });
    
     test('changeParam', function(){
        expect(2);
        
         var parsed3 = new UrlParser(url3);
        parsed3.setParams({'b' : '2'});
        equal(parsed3.getUrl(), "https://example.com/extension/module/action?b=2");
        
         var parsed4 = new UrlParser(url4);
        parsed4.addParam('b', '4');
        equal(parsed4.getUrl(), "https://example.com/?p=a&c=b&b=4");
    });
});


