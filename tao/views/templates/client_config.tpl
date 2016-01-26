require.config({

    baseUrl : '<?=get_data('tao_base_www')?>js',
    catchError: true,
    waitSeconds: <?=get_data('client_timeout')?>,

    config : {
        'context': {
            root_url                : '<?=ROOT_URL?>',
            base_url                : '<?=BASE_URL?>',
            taobase_www             : '<?=get_data('tao_base_www')?>',
            base_www                : '<?=get_data('base_www')?>',
            base_lang               : '<?=get_data('lang')?>',
            locale                  : '<?=get_data('locale')?>',
            extension               : '<?=get_data('extension')?>',
            module                  : '<?=get_data('module')?>',
            action                  : '<?=get_data('action')?>',
            shownExtension          : '<?=get_data('shownExtension')?>',
            shownStructure          : '<?=get_data('shownStructure')?>',
            extensionsLocales       : <?=json_encode(get_data('extensionsLocales'))?>,
            timeout                 : <?=get_data('client_timeout')?>,
        },
        text: {
            useXhr: function(){
                return true;
            }
        },

        'ui/themes' : <?= get_data('themesAvailable') ?>,
//dynamic lib config
    <?php foreach (get_data('libConfigs') as $name => $config) :?>
        '<?=$name?>'        : <?=json_encode($config)?>,
    <?php endforeach?>
    },

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
        'jquery.autocomplete'  : 'lib/jquery.autocomplete/jquery.autocomplete',
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
        'moment'            : 'lib/moment-with-locales.min',
        'handlebars'        : 'lib/handlebars',

        'class'             : 'lib/class',
        'raphael'           : 'lib/raphael/raphael',
        'scale.raphael'     : 'lib/raphael/scale.raphael',
        'spin'              : 'lib/spin.min',
        'history'           : 'lib/history/history',
        'mediaElement'      : 'lib/mediaelement/mediaelement-and-player',
        'mathJax'           : [
            '../../../taoQtiItem/views/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full',
            '../../../taoQtiItem/views/js/MathJaxFallback'
        ],
        'ckeditor'          : 'lib/ckeditor/ckeditor',

//locale loader
        'i18ntr'            : '../locales/<?=get_data('locale')?>',

//extension aliases, and controller loading in prod mode
    <?php foreach (get_data('extensionsAliases') as $name => $path) :?>
        '<?=$name?>'        : '<?=$path?>',
        <?php if(tao_helpers_Mode::is('production')):?>
        '<?=$name?>/controller/routes' : '<?=$path?>/controllers.min',
        <?php endif?>
    <?php endforeach?>
   },

   shim : {
        'wfEngine/wfApi/wfApi.min' : ['jquery'],
        'moment'                : { exports : 'moment' },
        'ckeditor'              : { exports : 'CKEDITOR' },
        'ckeditor-jquery'       : ['ckeditor'],
        'class'                 : { exports : 'Class'},

        'mathJax' : {
            exports : "MathJax",
            init : function(){
                if(window.MathJax){
                    MathJax.Hub.Config({showMathMenu:false, showMathMenuMSIE:false});//add mathJax config here
                    MathJax.Hub.Startup.onload();
                    return MathJax;
                }
            }
        }
    }
});
