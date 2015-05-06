define([
    'lodash',
    'taoQtiItem/qtiCreator/model/mixin/editable',
    'taoQtiItem/qtiItem/core/Container'
], function(_, editable, Container) {
    var methods = {};
    _.extend(methods, editable);
    _.extend(methods, {
        afterCreate: function() {
            this.body('Lorem ipsum dolor sit amet, consectetur adipisicing ...');
        }
    });
    return Container.extend(methods);
});