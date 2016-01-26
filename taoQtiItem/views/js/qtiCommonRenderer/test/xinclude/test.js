define(['taoQtiItem/qtiCommonRenderer/test/runner', 'lodash', 'taoQtiItem/qtiItem/helper/xincludeLoader'], function (runner, _, xincludeLoader){
    QUnit.start();

    var baseUrl = 'taoQtiItem/test/samples/qtiv2p1/associate_include/';
    runner.run({
        relBaseUrl : baseUrl,
        callback : function (item, renderer){

            var xincludes = _.values(item.getElements('include'));

            QUnit.equal(xincludes.length, 1, 'xinclude found');
            var xinclude = xincludes[0];

            QUnit.stop();
            xincludeLoader.load(xinclude, baseUrl, function (xi, data, loadedClasses){
                renderer.load(function (){

                    QUnit.start();
                    QUnit.ok(data.body.body, 'has body');
                    QUnit.equal(_.size(data.body.elements), 2, 'elment img & math loaded');
                    QUnit.equal(xi.qtiClass, 'include', 'qtiClass ok');

                    item.render(item.getContainer());
                    item.postRender();

                }, loadedClasses);
            });
        }
    });
});


