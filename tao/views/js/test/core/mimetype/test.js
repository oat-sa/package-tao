define(['core/mimetype'], function(mimeType){
    'use strict';

    test('protoype', function(){
       ok(typeof mimeType === 'object', 'The module mimeType expose an object');
       ok(typeof mimeType.getFileType === 'function', 'The module mimeType has a getFileType method');
    });

    test('getFileType', function(){
        equal(mimeType.getFileType({mime : 'application/ogg'}), 'video', 'Ogg files are video');
        equal(mimeType.getFileType({mime : 'audio/mp3'}), 'audio', 'Mp3 files are audio');
        equal(mimeType.getFileType({mime : 'text/css'}), 'css', 'Css mime type');
        equal(mimeType.getFileType({name : 'style.css'}), 'css', 'Css extension');
        equal(mimeType.getFileType({mime : 'text/plain'}), 'text', 'Text mime type');
    });
    
    test('getCategory', function(){
        equal(mimeType.getCategory('video'), 'media', 'video files are media');
        equal(mimeType.getCategory('css'), 'sources', 'css files are sources');
    });
});
