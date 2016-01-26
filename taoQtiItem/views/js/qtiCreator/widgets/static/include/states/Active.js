define([
    'jquery',
    'lodash',
    'i18n',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/static/states/Active',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/static/include',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'taoQtiItem/qtiCreator/helper/xincludeRenderer',
    'ui/resourcemgr'
], function($, _, __, stateFactory, Active, formTpl, formElement, xincludeRenderer){

    var IncludeStateActive = stateFactory.extend(Active, function(){

        this.initForm();

    }, function(){

        this.widget.$form.empty();
    });

    IncludeStateActive.prototype.initForm = function(){

        var _widget = this.widget,
            $form = _widget.$form,
            include = _widget.element,
            baseUrl = _widget.options.baseUrl;

        $form.html(formTpl({
            baseUrl : baseUrl || '',
            href : include.attr('href')
        }));

        //init slider and set align value before ...
        _initUpload(_widget);

        //... init standard ui widget
        formElement.initWidget($form);

    };

    var _initUpload = function(widget){

        var $form = widget.$form,
            options = widget.options,
            $uploadTrigger = $form.find('[data-role="upload-trigger"]'),
            $href = $form.find('input[name=href]');

        var _openResourceMgr = function(){
            $uploadTrigger.resourcemgr({
                title : __('Please select a shared stimulus file from the resource manager.'),
                appendContainer : options.mediaManager.appendContainer,
                mediaSourcesUrl : options.mediaManager.mediaSourcesUrl+'?exclude=local',
                browseUrl : options.mediaManager.browseUrl,
                uploadUrl : options.mediaManager.uploadUrl,
                deleteUrl : options.mediaManager.deleteUrl,
                downloadUrl : options.mediaManager.downloadUrl,
                fileExistsUrl : options.mediaManager.fileExistsUrl,
                disableUpload : true,
                params : {
                    uri : options.uri,
                    lang : options.lang,
                    filters : 'application/qti+xml'
                },
                pathParam : 'path',
                select : function(e, files){

                    var file;

                    if(files && files.length){

                        file = files[0].file;
                        
                        //set the selected file as the new href and refresh rendering
                        xincludeRenderer.render(widget, options.baseUrl, file);

                        _.defer(function(){
                            $href.val(file).trigger('change');
                        });
                    }
                },
                open : function(){
                    //hide tooltip if displayed
                    if($href.hasClass('tooltipstered')){
                        $href.blur().tooltipster('hide');
                    }
                },
                close : function(){
                    //triggers validation : 
                    $href.blur();
                }
            });
        };
        
        $uploadTrigger.on('click', _openResourceMgr);
        $href.on('click', _openResourceMgr);//href input is read only

        //if empty, open file manager immediately
        if(!$href.val()){
            _openResourceMgr();
        }

    };

    return IncludeStateActive;
});
