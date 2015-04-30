require(['config'], function(){

    require[
        'i18n',
        'lodash',
        'handlebars',
        'jquery',
        'common',
        'magnifier',
        'protractor',
        'ruler',
        'scientific-calculator',
        'simple-calculator',
        'launcher',
        'jqueryui'
    ], 
    function(
        __,
        _,
        tpl,
        $,
        common,
        magnifier,
        protractor,
        ruler,
        scientificCalculator,
        simpleCalculator,
        launcher
        ){

    }

    if(!document.implementation.hasFeature('http://www.w3.org/TR/SVG11/feature#Image', '1.1')) {
        var scope = document.querySelectorAll('.sts-scope'),
        i = scope.length;
        while(i--) {
            scope[i].className += ' sts-no-svg';
        }
    }
});
