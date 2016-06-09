define(['core/mimetype'], function(mimeType){
    'use strict';

    QUnit.test('protoype', function(assert){
       assert.ok(typeof mimeType === 'object', 'The module mimeType expose an object');
       assert.ok(typeof mimeType.getFileType === 'function', 'The module mimeType has a getFileType method');
       assert.ok(typeof mimeType.getResourceType === 'function', 'The module mimeType has a getResourceType method');
    });

    QUnit.test('getFileType', function(assert){
        assert.equal(mimeType.getFileType({mime : 'application/ogg'}), 'video', 'Ogg files are video');
        assert.equal(mimeType.getFileType({mime : 'audio/mp3'}), 'audio', 'Mp3 files are audio');
        assert.equal(mimeType.getFileType({mime : 'text/css'}), 'css', 'Css mime type');
        assert.equal(mimeType.getFileType({name : 'style.css'}), 'css', 'Css extension');
        assert.equal(mimeType.getFileType({mime : 'text/plain'}), 'text', 'Text mime type');
    });

    QUnit.test('getCategory', function(assert){
        assert.equal(mimeType.getCategory('video'), 'media', 'video files are media');
        assert.equal(mimeType.getCategory('css'), 'sources', 'css files are sources');
    });

    var resources = [
        { url: '/tao/views/js/test/core/mimetype/samples/audio.mp3', type: 'audio/mpeg', error: false, title : 'MP3' },
        { url: '/tao/views/js/test/core/mimetype/samples/video.mp4', type: 'video/mp4', error: false, title : 'MP4' },
        { url: '/tao/views/js/test/core/mimetype/samples/unknown', type: null, error: true, title : 'Unknown resource' }
    ];

    QUnit
        .cases(resources)
        .asyncTest('getResourceType ', function(data, assert) {
            mimeType.getResourceType(data.url, function(err, type) {
                assert.equal(!!err, data.error, 'The callback accept an error');
                if (!data.error) {
                    assert.equal(type, data.type, 'The callback received the correct MIME type');
                }
                QUnit.start();
            });
        });
});
