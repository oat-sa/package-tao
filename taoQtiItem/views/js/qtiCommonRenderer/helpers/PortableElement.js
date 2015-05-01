define(['context', 'lodash', 'jquery', 'taoQtiItem/qtiItem/helper/util'], function(context, _, $, util){

    /**
     * Get the location of the document, useful to define a baseUrl for the required context
     * @returns {String}
     */
    function getDocumentBaseUrl(){
        return window.location.protocol + '//' + window.location.host + window.location.pathname.replace(/([^\/]*)$/, '');
    }

    /**
     * Get root url of available vendor specific libraries 
     * 
     * @returns {object} - an "associative" array object
     */
    function getSharedLibrariesPaths(){
        var requireConfig = window.require.s.contexts._.config;
        return getAbsolutelyDefinedPaths(requireConfig.baseUrl, requireConfig.paths);
    }

    /**
     * Get lists of required OAT delivery engine libs
     * 
     * @returns {Object}
     */
    function getCommonLibraries(){
        return {
            css : context.root_url + 'tao/views/js/lib/require-css/css',
            mathJax : [
                context.root_url + 'taoQtiItem/views/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full',
                context.root_url + 'taoQtiItem/views/js/MathJaxFallback'
            ]
        };
    }

    /**
     * Replace all identified relative media urls by the absolute one
     * 
     * @param {String} markupStr
     * @param {Object} the renderer
     * @returns {String}
     */
    function fixMarkupMediaSources(markupStr, renderer){

        //load markup string into a div container
        var $markup = $('<div>', {'class' : 'wrapper'}).html(markupStr);

        //for each media source
        $markup.find('img').each(function(){

            var $img = $(this),
                src = $img.attr('src'),
                fullPath = renderer.getAbsoluteUrl(src);

            $img.attr('src', fullPath);
        });

        return $markup.html();

    }

    /**
     * Transform relative paths in the "paths" argument object into absolute ones
     * 
     * @param {String} baseUrl
     * @param {Object} paths
     * @returns {Object}
     */
    function getAbsolutelyDefinedPaths(baseUrl, paths){
        var ret = {};
        _.forIn(paths, function(path, alias){
            //take only namespaced module
            if(alias.indexOf('/')>0){
                ret[alias] = baseUrl+path;
            }
        });
        return ret;
    }

    /**
     * Get a local require js with typeIdentifier as specific context
     * 
     * @param {String} typeIdentifier
     * @param {String} baseUrl
     * @param {Object} libs
     * @returns {Function} - RequireJs instance
     */
    function getLocalRequire(typeIdentifier, baseUrl, libs, config){

        config = config || {};

        var runtimeLocation = config.runtimeLocation ? config.runtimeLocation : baseUrl + typeIdentifier;
        var requireConfig = window.require.s.contexts._.config;

        if(config.useExtensionAlias){
            var urlTokens = baseUrl.split('/');
            var extension = urlTokens[0];

            var fullpath = requireConfig.baseUrl + requireConfig.paths[extension];

            //update baseUrl:
            baseUrl = baseUrl.replace(extension, fullpath);

            if(config.runtimeLocation){
                runtimeLocation = config.runtimeLocation.replace(extension, fullpath);
            }
        }

        libs = libs || {};
        libs = _.defaults(libs, getCommonLibraries());
        libs = _.defaults(libs, getSharedLibrariesPaths());

        //add local namespace
        libs[typeIdentifier] = runtimeLocation;//allow overwrite by config (in test)

        return window.require.config({
            context : typeIdentifier, //use unique typeIdentifier as context name
            baseUrl : baseUrl,
            paths : libs,
            shim : {
                mathJax : {
                    exports : "MathJax",
                    init : function(){
                        if(window.MathJax){
                            MathJax.Hub.Config({showMathMenu : false, showMathMenuMSIE : false});//add mathJax config here for now before integrating the amd one
                            MathJax.Hub.Startup.onload();
                            return MathJax;
                        }
                    }
                }
            }
        });
    }

    /**
     * local require js caches
     * 
     * @type Object
     */
    var _localRequires = {};

    /**
     * Get a cached local require js with typeIdentifier as specific context
     * If it does not exsits, it creates one.
     * Warning only the typeIdentifier and baseUrl will be used as key (not libs)
     * This means that if you want to ensure that the baseUrl and libs are different,
     * you may want to use getLocalRequire instead
     * 
     * @param {String} typeIdentifier
     * @param {String} baseUrl
     * @param {Object} libs
     * @returns {Function} - RequireJs instance
     */
    function getCachedLocalRequire(typeIdentifier, baseUrl, libs, config){

        _localRequires[typeIdentifier] = _localRequires[typeIdentifier] || {};
        if(!_localRequires[typeIdentifier][baseUrl]){
            _localRequires[typeIdentifier][baseUrl] = getLocalRequire(typeIdentifier, baseUrl, libs, config);
        }
        return _localRequires[typeIdentifier][baseUrl];
    }

    return {
        getSharedLibrariesPaths : getSharedLibrariesPaths,
        getCommonLibraries : getCommonLibraries,
        fixMarkupMediaSources : fixMarkupMediaSources,
        getDocumentBaseUrl : getDocumentBaseUrl,
        getLocalRequire : getLocalRequire,
        getCachedLocalRequire : getCachedLocalRequire
    };
});