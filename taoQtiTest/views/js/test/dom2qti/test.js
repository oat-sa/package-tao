define(['jquery', 'taoQtiTest/controller/creator/encoders/dom2qti'], function($, Dom2Qti){
    'use strict';

    var domStrs = [], models = [];
    QUnit.module('DomObject encoder', {
        setup: function() {

            domStrs[0] = '<div id="main-container" class="container">' +
		'<h1>The F4U Corsair</h1>' +
		'<img src="f4u-corsair.png" alt="A restored F4U-4 Corsair in Korean War-ear U.S. Marine Corps markings" longdesc="http://en.wikipedia.org/wiki/Vought_F4U_Corsair" height="400" width="300"/>' +
		'<h2>Introduction</h2>' +
		'<p>' +
			'The <strong>Chance Vought F4U Corsair</strong> was a carrier-capable fighter aircraft ' +
			'that saw service primarily in World War II and the Korean War. Demand ' +
			'for the aircraft soon overwhelmed Vought\'s manufacturing capability, ' +
			'resulting in production by Goodyear and Brewster: Goodyear-built Corsairs ' +
			'were designated <strong>FG</strong> and Brewster-built aircraft <strong>F3A</strong>. ' +
			'From the first prototype delivery to the U.S. Navy in 1940, to final delivery in 1953 ' +
			'to the French, 12,571 F4U Corsairs were manufactured by Vought,[1] in 16 separate models, ' +
			'in the longest production run of any piston-engined fighter in U.S. history (1942–53).€ '+
		'</p>' +
            '</div>';

            domStrs[1] = '<p>paragraph 1</p>' +
            '<p>paragraph 2</p>' +
            '<p>paragraph 3</p>';

            models[0] = {
                "qti-type": "div",
                "xmlBase": "",
                "id": "main-container",
                "class": "container",
                "lang": "",
                "label": "",
                "content": [{
                        "qti-type": "h1",
                        "xmlBase": "",
                        "content": [{
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": "The F4U Corsair"
                        }],
                        "id": "",
                        "class": "",
                        "lang": "",
                        "label": ""
                    }, {
                        "qti-type": "img",
                        "src": "f4u-corsair.png",
                        "alt" : "A restored F4U-4 Corsair in Korean War-ear U.S. Marine Corps markings",
                        "longdesc": "http:\/\/en.wikipedia.org\/wiki\/Vought_F4U_Corsair",
                        "height": "400",
                        "width": "300",
                        "id": "",
                        "class": "",
                        "lang": "",
                        "label": "",
                        "xmlBase": ""
                    }, {
                        "qti-type": "h2",
                        "xmlBase": "",
                        "content": [{
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": "Introduction"
                        }],
                        "id": "",
                        "class": "",
                        "lang": "",
                        "label": ""
                    }, {
                        "qti-type": "p",
                        "xmlBase": "",
                        "content": [{
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": "The "
                        }, {
                            "qti-type": "strong",
                            "xmlBase": "",
                            "content": [{
                                "qti-type": "textRun",
                                "xmlBase": "",
                                "content": "Chance Vought F4U Corsair"
                            }],
                            "id": "",
                            "class": "",
                            "lang": "",
                            "label": ""
                        }, {
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": " was a carrier-capable fighter aircraft that saw service primarily in World War II and the Korean War. Demand for the aircraft soon overwhelmed Vought's manufacturing capability, resulting in production by Goodyear and Brewster: Goodyear-built Corsairs were designated "
                        }, {
                            "qti-type": "strong",
                            "xmlBase": "",
                            "content": [{
                                "qti-type": "textRun",
                                "xmlBase": "",
                                "content": "FG"
                            }],
                            "id": "",
                            "class": "",
                            "lang": "",
                            "label": ""
                        }, {
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": " and Brewster-built aircraft "
                        }, {
                            "qti-type": "strong",
                            "xmlBase": "",
                            "content": [{
                                "qti-type": "textRun",
                                "xmlBase": "",
                                "content": "F3A"
                            }],
                            "id": "",
                            "class": "",
                            "lang": "",
                            "label": ""
                        }, {
                            "qti-type": "textRun",
                            "xmlBase": "",
                            "content": ". From the first prototype delivery to the U.S. Navy in 1940, to final delivery in 1953 to the French, 12,571 F4U Corsairs were manufactured by Vought,[1] in 16 separate models, in the longest production run of any piston-engined fighter in U.S. history (1942\u201353).€ "
                        }],
                        "id": "",
                        "class": "",
                        "lang": "",
                        "label": ""
                    }]
                };
             models[1] = [{
                "qti-type": "p",
                "xmlBase": "",
                "id": "",
                "class": "",
                "lang": "",
                "label": "",
                "content": [{
                    "qti-type": "textRun",
                    "xmlBase": "",
                    "content": "paragraph 1"
                }]}, {
                "qti-type": "p",
                "xmlBase": "",
                "id": "",
                "class": "",
                "lang": "",
                "label": "",
                "content": [{
                    "qti-type": "textRun",
                    "xmlBase": "",
                    "content": "paragraph 2"
                }]}, {
                "qti-type": "p",
                "xmlBase": "",
                "id": "",
                "class": "",
                "lang": "",
                "label": "",
                "content": [{
                    "qti-type": "textRun",
                    "xmlBase": "",
                    "content": "paragraph 3"
                }]
            }];
        }
    });

    QUnit.test('encode', function(assert){
        QUnit.expect(2);

        assert.ok(typeof Dom2Qti.encode === 'function');

        var result = Dom2Qti.encode(models[0]);

        var pattern = /\s/g;

        assert.equal(result.replace(pattern, ''), domStrs[0].replace(pattern, ''));
    });

    QUnit.test('decode', function(assert){
        QUnit.expect(2);

        assert.ok(typeof Dom2Qti.decode === 'function');

        var result = Dom2Qti.decode(domStrs[0].replace(/\s+/gm, ' '));

        assert.deepEqual(result, [models[0]]);
    });

    QUnit.test('encode multi roots', function(assert){
        QUnit.expect(2);

        assert.ok(typeof Dom2Qti.encode === 'function');

        var result = Dom2Qti.encode(models[1]);

        var pattern = /\s/g;

        assert.equal(result.replace(pattern, ''), domStrs[1].replace(pattern, ''));
    });

    test('decode multi roots', function(assert){
        QUnit.expect(2);

        assert.ok(typeof Dom2Qti.decode === 'function');

        var result = Dom2Qti.decode(domStrs[1].replace(/\s+/gm, ' '));

        assert.deepEqual(result, models[1]);
    });
});


