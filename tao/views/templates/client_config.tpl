require.config({

    baseUrl : '<?=TAOBASE_WWW?>js',
    catchError: true,
    waitSeconds:10,
    
    config : {
        'context': {
            root_url        : '<?=ROOT_URL?>',
            base_url        : '<?=BASE_URL?>',
            taobase_www     : '<?=TAOBASE_WWW?>',
            base_www        : '<?=get_data('base_www')?>',
            base_lang       : '<?=get_data('lang')?>',
            locale          : '<?=get_data('locale')?>',
            extension       : '<?=get_data('extension')?>',
            module          : '<?=get_data('module')?>',
            action          : '<?=get_data('action')?>',
            shownExtension  : '<?=get_data('shownExtension')?>',
            shownStructure  : '<?=get_data('shownStructure')?>',
            extensionsLocales     : <?=json_encode(get_data('extensionsLocales'))?>
        }
    },
    
    paths : {
        'jquery'            : 'lib/jquery-1.8.0.min',
        'jqueryui'          : 'lib/jquery-ui-1.8.23.custom.min',
        'text'              : 'lib/text/text',
        'json'              : 'lib/text/json',
        'css'               : 'lib/require-css/css',
        'polyfill'          : 'lib/polyfill',
        'filereader'        : 'lib/polyfill/jquery.FileReader.min',
        'store'             : 'lib/store/store.min',
        'select2'           : 'lib/select2/select2.min',
        'lodash'            : 'lib/lodash.min',
        'async'             : 'lib/async',
        'moment'            : 'lib/moment.min',
        'handlebars'        : 'lib/handlebars',
        'tpl'               : 'tpl',
        'ckeditor'          : 'lib/ckeditor/ckeditor',
        'ckConfigurator'    : '../../../taoQtiItem/views/js/qtiCreator/editor/ckEditor/ckConfigurator',
        'class'             : 'lib/class',
        'jwysiwyg'          : 'lib/jwysiwyg/jquery.wysiwyg',
        'jsTree'            : 'lib/jsTree',
        'jqGrid'            : 'lib/jquery.jqGrid-4.4.0/js/jquery.jqGrid.min',
        'jquery.timePicker' : 'lib/jquery.timePicker',
        'jquery.cookie'     : 'lib/jquery.cookie',
        'attrchange'        : 'lib/attrchange',
        'raphael'           : 'lib/raphael/raphael',
        'scale.raphael'     : 'lib/raphael/scale.raphael',
        'raphael-collision' : 'lib/raphael/raphael-collision/raphael-collision',
        'spin'              : 'lib/spin.min',
        'tooltipster'       : 'lib/tooltipster/js/jquery.tooltipster.min',
        'nouislider'        : 'lib/sliders/jquery.nouislider',
        'jquery.trunc'		: 'lib/jquery.badonkatrunc',
        'i18n_tr'           : '<?=BASE_URL?>locales/<?=get_data('locale')?>/messages_po',
    <?foreach (get_data('extensionsAliases') as $name => $path) :?>
        '<?=$name?>'        : '<?=$path?>',
        '<?=$name?>_css'        : '../../../<?=$name?>/views/css',
        <?if(in_array($name, get_data('extensionsLocales'))):?>
        '<?=$name?>_i18n'   : '../../../<?=$name?>/locales/<?=get_data('locale')?>/messages_po',
        <?endif?>
        <?if(tao_helpers_Mode::is('production')):?>
        '<?=$name?>/controller/routes' : '<?=$path?>/controllers.min',
        <?endif?>
    <?endforeach?>
        'mediaElement'      : 'lib/mediaelement/mediaelement-and-player.min',
        'mathJax'           : [
            '../../../taoQtiItem/views/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full',
            '../../../taoQtiItem/views/js/MathJaxFallback'
        ],
        'jquery.fmRunner'   : '../../../filemanager/views/js/jquery.fmRunner',
        'eyecatcher'        : 'eyecatcher',
        'jquery.fileDownload'  : 'lib/jquery.fileDownload'
   },
  
   shim : {
        'jqueryui'              : ['jquery'],
        'jquerytools'           : ['jquery'],
        'select2'               : ['jquery'],
        'jwysiwyg'              : ['jquery'],
        'jquery.cookie'         : ['jquery'],
        'jquery.trunc'			: ['jquery'],
        'jquery.timePicker'     : ['jquery'],
        'tooltipster'           : ['jquery', 'css!lib/tooltipster/css/tooltipster'],
        'nouislider'            : ['jquery'],
        'jquery.fileDownload'   : ['jquery'],
        'jsTree/plugins/jquery.tree.contextmenu' : ['lib/jsTree/jquery.tree'],
        'jsTree/plugins/jquery.tree.checkbox' : ['lib/jsTree/jquery.tree'],
        'generis.tree.select'   : ['generis.tree', 'lib/jsTree/plugins/jquery.tree.checkbox'],
        'generis.tree.browser'  : ['generis.tree', 'jsTree/plugins/jquery.tree.contextmenu'],
        'jqGrid'                : ['jquery', 'lib/jquery.jqGrid-4.4.0/js/i18n/grid.locale-<?=get_data('lang')?>'],
        'attrchange'            : ['jquery'],
        'grid/tao.grid'         : ['jqGrid'],
        'grid/tao.grid.downloadFileResource' : ['grid/tao.grid'],
        'grid/tao.grid.rowId'   : ['grid/tao.grid'],
        'AsyncFileUpload'       : ['lib/jquery.uploadify/swfobject', 'lib/jquery.uploadify/jquery.uploadify.v2.1.4.min'],
        'jquery.fmRunner'       : ['jquery', 'filemanager/fmRunner'],
        'filemanager/jqueryFileTree/jqueryFileTree' : ['jquery'],
        'wfEngine/wfApi/wfApi.min' : ['jquery'],
        'handlebars'            : { exports : 'Handlebars' },
        'moment'                : { exports : 'moment' },
        'ckeditor'              : { exports : 'CKEDITOR' },
        'ckeditor-jquery'       : ['ckeditor'],
        'class'                 : { exports : 'Class'},
        'mediaElement' : {
            deps: ['jquery', 'css!lib/mediaelement/css/mediaelementplayer.min'],
            exports : 'MediaElementPlayer',
            init : function(){
                MediaElementPlayer.pluginPath = '<?=TAOBASE_WWW?>js/lib/mediaelement/'; //define the plugin swf path here
                MediaElementPlayer.plugins = ['flash'];
                MediaElementPlayer.flashName = 'flashmediaelement.swf';
                return MediaElementPlayer;
            }
        },
        'mathJax' : {
            exports : "MathJax",
            init : function(){
                if(window.MathJax){
                    MathJax.Hub.Config({showMathMenu:false, showMathMenuMSIE:false});//add mathJax config here
                    MathJax.Hub.Startup.onload();
                    return MathJax;
                }
            }
        },
        'filereader' : ['jquery', 'polyfill/swfobject']
    }
});
