define([
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Sleep',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'taoQtiItem/qtiCreator/widgets/states/Inactive',
    'taoQtiItem/qtiCreator/widgets/states/Deleting'
], function(factory){
    return factory.createBundle(arguments);
});