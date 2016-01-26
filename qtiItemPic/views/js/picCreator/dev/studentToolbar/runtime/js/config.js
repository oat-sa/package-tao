require.config({

    baseUrl : '.',
    catchError: true,
    waitSeconds: 10,
    
    paths : {
        'OAT'                   : 'widgets/OAT',       // open source widgets + common, launcher, tpl
        'PARCC'                 : 'widgets/PARCC',     // proprietary widgets (calculator etc.)
        'IMSGlobal'             : 'widgets/IMSGlobal', // jquery, lodash, handlebars, jqueryui...
        'tpl'                   : 'OAT/common/js/tpl'
   },
  
   shim : { 
        'IMSGlobal/jqueryui'              : ['jquery'],
        'handlebars'            : { exports : 'Handlebars' }
    }
});