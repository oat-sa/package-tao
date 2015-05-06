define(['jquery', 'ui', 'ui/uploader', 'ui/deleter'], function($, ui, uploader, deleter){

    deleter('.tao-scope');

    $('#upload-container').uploader({
        uploadUrl   : 'http://tao26.localdomain/taoItems/ItemContent/upload?uri=' + encodeURIComponent('http://tao26.localdomain/bertao.rdf#i1404285379397511') + '&lang=en-US&path=/',
        multiple    : true,
        upload      : true,
        read        : false
    });
 
});
