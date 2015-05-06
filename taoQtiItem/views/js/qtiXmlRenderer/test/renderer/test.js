require([
    'jquery',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer',
    'json!taoQtiItem/qtiItem/../../../test/samples/json/ALL.json'
], function($, Loader, Element, Renderer, data){

    var CL = console.log;

    function formatXml(xml){
        return vkbeautify.xml(xml, '\t');
    }

    test('render', function(){

        var identifier,
            itemData,
            loader = new Loader(),
            renderer = new Renderer({
            shuffleChoices : false,
            runtimeContext : {
                    runtime_base_www : '/taoQtiItem/test/samples/test_base_www/',
                    root_url : '',
                    debug : true
                }
            });

        for(var identifier in data){

            if(identifier !== 'graphicGapfill'){
                continue;
            }
            
            stop();//wait for the next start()
            
            itemData = data[identifier].full;

            loader.loadItemData(itemData, function(item){

                ok(Element.isA(item, 'assessmentItem'), identifier + ' item loaded');

                //set renderer
                item.setRenderer(renderer);

                //append item placeholder and render it:
                var $placeholder = $('<div>', {id : 'qtiItem-' + item.id()});
                $("#qunit-fixture").after($placeholder);

                $placeholder.before($('<h2>', {text : 'identifier : ' + item.id()}));
                $placeholder.wrap('<pre class="line-numbers"><code class="language-markup"></code></pre>');

                renderer.load(function(){

                    start();
                    var xml = item.render();
                    xml = formatXml(xml);
                    xml = xml
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");

                    var $code = $placeholder.parent().html(xml);
                    Prism.highlightElement($code[0]);

                }, this.getLoadedClasses());

            });

        }

    });

});