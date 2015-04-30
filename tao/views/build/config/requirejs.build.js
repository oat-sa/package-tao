require.config({

    baseUrl : '../js',
    paths : {

//require-js plugins
        'text'              : 'lib/text/text',
        'json'              : 'lib/text/json',
        'css'               : 'lib/require-css/css',
        'tpl'               : 'tpl',

//jquery and plugins
        'jquery'            : 'lib/jquery-1.8.0.min',
        'jqueryui'          : 'lib/jquery-ui-1.8.23.custom.min',
        'select2'           : 'lib/select2/select2.min',
        'jwysiwyg'          : 'lib/jwysiwyg/jquery.wysiwyg',
        'jquery.tree'       : 'lib/jsTree/jquery.tree',
        'jquery.timePicker' : 'lib/jquery.timePicker',
        'jquery.cookie'     : 'lib/jquery.cookie',
        'tooltipster'       : 'lib/tooltipster/jquery.tooltipster',
        'nouislider'        : 'lib/sliders/jquery.nouislider',
        'jquery.trunc'		: 'lib/jquery.badonkatrunc',
        'jquery.fileDownload'  : 'lib/jquery.fileDownload',

//polyfills
        'polyfill'          : 'lib/polyfill',
        'filereader'        : 'lib/polyfill/jquery.FileReader.min',

//libs
        'store'             : 'lib/store/store.min',
        'lodash'            : 'lib/lodash.min',
        'async'             : 'lib/async',
        'moment'            : 'lib/moment.min',
        'handlebars'        : 'lib/handlebars',

        'class'             : 'lib/class',
        'raphael'           : 'lib/raphael/raphael',
        'scale.raphael'     : 'lib/raphael/scale.raphael',
        'spin'              : 'lib/spin.min',
        'history'           : 'lib/history',

        'mediaElement'      : 'lib/mediaelement/mediaelement-and-player.min',
        'mathJax'           : '../../../taoQtiItem/views/js/mathjax/MathJax',
        'ckeditor'          : 'lib/ckeditor/ckeditor',

//optimizer needed
        'css-builder'       : 'lib/require-css/css-builder',
        'normalize'         : 'lib/require-css/normalize',

//stub
        'i18ntr'            : '../locales/en-US'
   },

   shim : {
        'wfEngine/wfApi/wfApi.min' : ['jquery'],
        'moment'                : { exports : 'moment' },
        'ckeditor'              : { exports : 'CKEDITOR' },
        'ckeditor-jquery'       : ['ckeditor'],
        'class'                 : { exports : 'Class'},
    }
});
