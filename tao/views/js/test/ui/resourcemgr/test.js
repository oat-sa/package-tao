define(['jquery', 'helpers', 'ui/resourcemgr'], function($, helpers){

    //sample

    var $uri = $('#uri');
    var $launcher = $('#launcher');
    $launcher.attr('disabled', 'disabled');

    $uri.on('change', function(){
        if($uri.val() === ''){
            $launcher.attr('disabled', 'disabled');
        } else {
            $launcher.removeAttr('disabled');
        } 
    });
    $uri.trigger('change');

    $launcher.click(function(e){
        e.preventDefault();

        $launcher.resourcemgr({
            root        : '/',
            browseUrl   : helpers._url('files', 'ItemContent', 'taoItems'),
            uploadUrl   : helpers._url('upload', 'ItemContent', 'taoItems'),
            deleteUrl   : helpers._url('delete', 'ItemContent', 'taoItems'),
            downloadUrl : helpers._url('download', 'ItemContent', 'taoItems'),
            params : {
                filters : $('#filters').val(),
                uri : $('#uri').val(),
                lang : 'en-US'
            },
            pathParam : 'path',
            create : function(e){
                console.log('created');
            },
            open : function(e){
                console.log('opened');
            },
            close : function(e){
                console.log('closed');
            },
            select : function(e, files){
                $('.selected').text('Resources selected: ' + JSON.stringify(files)); 
            }
        }); 
   });


    //test

    module('ResourceManager Stand Alone Test');
   
    test('plugin', function(){
        expect(1);
        ok(typeof $.fn.resourcemgr === 'function', 'The resourcemgr plugin is loaded');
    });

});


