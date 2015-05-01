require([
    'jquery',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiDefaultRenderer/renderers/Renderer',
    'json!taoQtiItem/qtiItem/../../../test/samples/json/ALL.json'
], function($, Loader, Element, Renderer, data){

    var CL = console.log;
    test('render', function(){

        var loader = new Loader();
        var renderer = new Renderer({
            runtimeContext : {
                runtime_base_www : '/taoQtiItem/test/samples/test_base_www/',
                root_url : '',
                debug : true
            }
        });

        for(var identifier in data){
            
            if(identifier === 'upload'){
                //uploadInteraction not implemented in the default renderer
                continue;
            }
            
            stop();//wait for the next start()

            loader.loadItemData(data[identifier].full, function(item){

                ok(Element.isA(item, 'assessmentItem'), identifier + ' item loaded');

                //set renderer
                item.setRenderer(renderer);

                //append item placeholder and render it:
                var $placeholder = $('<div>', {id : 'qtiItem-' + item.id()});
                var $title = $('<h2>', {text : 'identifier : ' + item.id()});
                $("#qunit-fixture").after($placeholder.before($title));

                renderer.load(function(){

                    start();
                    try{
                        item.render({}, $placeholder);
                    }catch(e){
                        CL('error in template rendering', e);
                    }

                    //check item container:
                    var $container = $('#' + item.id());
                    ok($container.length, 'rendered container found');

                    //post render
                    try{
                        item.postRender();
                    }catch(e){
                        CL('error in post rendering', e);
                    }
                    
                }, this.getLoadedClasses());

            });

//            break;
        }

    });

});