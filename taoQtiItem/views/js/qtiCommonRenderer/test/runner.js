define([
    'lodash',
    'jquery',
    'helpers',
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiCommonRenderer/renderers/Renderer'
], function(_, $, helpers, Loader, Element, Renderer){

    var _responseEqual = function(actual, expected, ordered){
        ordered = ordered || false;
        var responseStr = JSON.stringify(expected);
        if(actual.base || actual.list && ordered){
            deepEqual(actual, expected, 'response matches : ' + responseStr);
        }else if(actual.list && expected.list){
            var _isListEqual = function(actualList, expectedList){
                var ret = true;
                for(var i in actualList){
                    if(typeof (actualList[i]) === 'object'){
                        ret = _isListEqual(actualList[i], expectedList[i]);
                    }else{
                        ret = (!_.difference(actual.list[i], expected.list[i]).length);
                    }
                    if(!ret){
                        break;
                    }
                }
                return ret;
            };
            ok(_isListEqual(actual.list, expected.list), 'unordered listed response matches :' + responseStr);
        }else{
            deepEqual(actual, expected, 'special reponse format matching : ' + responseStr);
        }
    };

    function runTest(itemData, config){

        test('render', function(){

            var assertions = config.assertions || {};
            var loader = new Loader();

            var renderer = new Renderer();
            renderer.getAssetManager().setData('baseUrl', config.baseUrl);

            //allow specifying the runtimeLocation (useful in debug mode)
            if(config.runtimeLocations){
                renderer.setOption('runtimeLocations', config.runtimeLocations);
            }

            stop();//wait for the next start()

            loader.loadItemData(itemData, function(item){

                ok(Element.isA(item, 'assessmentItem'), item.attr('identifer') + ' item loaded');

                //count interaction number:
                var interactions = item.getInteractions();
                equal(interactions.length, assertions.interactionCount, 'has ' + assertions.interactionCount + ' interaction(s)');

                //test only the last interaction:
                var interaction = interactions.pop();

                //append item placeholder and render it:
                var $placeholder = $('<div>', {id : 'qtiItem-' + item.id()});
                var $title = $('<h2>', {text : 'identifier : ' + item.id()});
                $("#qunit-fixture").after($placeholder.before($title));

                renderer.load(function(){

                    start();

                    //set renderer
                    item.setRenderer(this);

                    //render tpl:
                    try{
                        item.render({}, $placeholder);
                    }catch(e){
                        console.log('error in template rendering', e);
                    }

                    //check item container:
                    ok(item.getContainer().length, 'rendered container found');

                    //post render:
                    try{
                        item.postRender();
                    }catch(e){
                        console.log('error in post rendering', e);
                    }

                    if(_.isArray(assertions.responses)){

                        var $interactionContainer = interaction.getContainer();
                        ok($interactionContainer.length, 'interaction container ok');
                        $interactionContainer.on('responseSet', function(e, i, r){
                            equal(i.serial, interaction.serial);
                            ok(_.isPlainObject(r));
                        });

                        //test responses set() and get():
                        _.each(assertions.responses, function(response){
                            interaction.resetResponse();
                            interaction.setResponse(response.set ? response.set : response);//assign the given value
                            _responseEqual(interaction.getResponse(), response.get ? response.get : response);//test the assigned value
                            interaction.resetResponse();
                        });
                    }
                    if(_.isFunction(config.callback)){
                        config.callback(item, this);
                    }
                }, this.getLoadedClasses());

            });

        });
    }

    function parseXml(xml, callback){

        $.ajax({
            url : helpers._url('getJson', 'Parser', 'taoQtiItem'),
            type : 'POST',
            contentType : 'text/xml',
            dataType : 'json',
            data : xml
        }).done(callback);
    }

    var _defaultAssertions = {
        interactionCount : 1,
        responses : []
    };

    function buildTestConfig(globalConfig){

        var urlTokens = globalConfig.relBaseUrl.split('/');
        var extension = urlTokens[0];
        var fullpath = require.s.contexts._.config.paths[extension];
        var baseUrl = globalConfig.relBaseUrl.replace(extension, fullpath);
        
        var testConfig = {
            baseUrl : baseUrl,
            assertions : _.defaults(globalConfig.assertions || {}, _defaultAssertions)
        };

        if(globalConfig.runtimeLocations){
            testConfig.runtimeLocations = {};
            _.forIn(globalConfig.runtimeLocations, function(path, ns){
                testConfig.runtimeLocations[ns] = path.replace(extension, fullpath);
            });
        }
        
        if(_.isFunction(globalConfig.callback)){
            testConfig.callback = globalConfig.callback;
        }

        return testConfig;
    }

    var Test = {
        run : function(globalConfig){

            globalConfig = globalConfig || {};

            //require xml :
            require(['text!' + globalConfig.relBaseUrl + 'qti.xml'], function(xml){

                parseXml(xml, function(r){

                    if(r.itemData){
                        runTest(r.itemData, buildTestConfig(globalConfig));
                    }else{
                        throw 'qti xml parsing failed';
                    }
                });

            });
        }
    };

    return Test;
});
