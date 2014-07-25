define([
    'jquery',
    'lodash',
    'taoQtiItem/qtiCreator/widgets/states/factory',
    'taoQtiItem/qtiCreator/widgets/interactions/blockInteraction/states/Question',
    'taoQtiItem/qtiCreator/widgets/helpers/formElement',
    'tpl!taoQtiItem/qtiCreator/tpl/forms/interactions/media',
    'taoQtiItem/qtiCommonRenderer/renderers/interactions/MediaInteraction',
    'ui/resourcemgr'
], function($, _, stateFactory, Question, formElement, formTpl, MediaInteractionCommonRenderer) {
    
    var MediaInteractionStateQuestion = stateFactory.extend(Question,
        _.noop,
        function exitSleepState() {
            var widget = this.widget;
            if(widget.mediaElementObject){
                   widget.mediaElementObject.stop();
            }   
        }
    );

    MediaInteractionStateQuestion.prototype.initForm = function(){
        
        var _widget = this.widget,
            $form = _widget.$form,
            options = _widget.options,
            interaction = _widget.element;
        
        //initialization binding
        //initialize your form here, you certainly gonna need to modify it:
        //append the form to the dom (this part should be almost ok)
        $form.html(formTpl({
            
            //tpl data for the interaction
            autostart : !!interaction.attr('autostart'),
            loop : !!interaction.attr('loop'),
            //minPlays : parseInt(interaction.attr('minPlays')),
            maxPlays : parseInt(interaction.attr('maxPlays')),
            
            //tpl data for the "object", this part is going to be reused by the "objectWidget", http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10173
            data:interaction.object.attr('data'),
            type:interaction.object.attr('type'),//use the same as the uploadInteraction, contact jerome@taotesting.com for this
            width:interaction.object.attr('width'),
            height:interaction.object.attr('height')
        }));

        formElement.initWidget($form);
        
        //init data change callbacks
        //var callbacks = formElement.getMinMaxAttributeCallbacks(this.widget.$form, 'minPlays', 'maxPlays');
        var callbacks = [];
        
        
        function xmlUpdateCheat(interaction) {
            // xml update cheat
            interaction.attr( 'responseIdentifier', interaction.attr('responseIdentifier') );
        }
        
        
        //callbacks.autostart = formElement.getAttributeChangeCallback();
        callbacks.autostart = function(interaction, attrValue, attrName) {
            //console.log('autostarta se promeni');
            interaction.attr(attrName, attrValue);
            reRenderMediaInteraction(interaction);
            xmlUpdateCheat(interaction);
        };
        
        //callbacks.loop = formElement.getAttributeChangeCallback();
        callbacks.loop = function(interaction, attrValue, attrName) {
            //console.log('loopa se promeni');
            interaction.attr(attrName, attrValue);
            reRenderMediaInteraction(interaction);
            xmlUpdateCheat(interaction);
        };
        
        
        //callbacks.maxPlays = formElement.getAttributeChangeCallback();
        callbacks.maxPlays = _.debounce( function(interaction, attrValue, attrName){
            interaction.attr(attrName, attrValue);
            reRenderMediaInteraction(interaction);
            xmlUpdateCheat(interaction);
        }, 1000 );
        
        //callbacks['width'] = formElement.getAttributeChangeCallback();
        callbacks.width = _.debounce( function(interaction, attrValue, attrName){
            interaction.object.attr(attrName, attrValue);
            reRenderMediaInteraction(interaction);
            xmlUpdateCheat(interaction);
        }, 1000 );
        
        callbacks.height = _.debounce( function(interaction, attrValue, attrName){
            interaction.object.attr(attrName, attrValue);
            reRenderMediaInteraction(interaction);
            xmlUpdateCheat(interaction);
        }, 1000 );
        
        
        
        function reRenderMediaInteraction(interaction) {
            if ( _widget.mediaElementObject !== undefined && _widget.mediaElementObject.src !== '' ) {
                _widget.mediaElementObject.setSrc('');
            }
            MediaInteractionCommonRenderer.destroy(interaction);
            //MediaInteractionCommonRenderer.destroy.call(interaction.getRenderer(), interaction);
            _widget.mediaElementObject = MediaInteractionCommonRenderer.render.call(interaction.getRenderer(), interaction);
        }
        
        
        callbacks.data = _.debounce( function(interaction, attrValue, attrName){
            if ( interaction.object.attr(attrName) !== attrValue ) {
                interaction.object.attr(attrName, attrValue);
                xmlUpdateCheat(interaction);

                var dataValue = attrValue.trim().toLowerCase();
                if ( dataValue.indexOf('http://www.youtube.com') === 0 || dataValue.indexOf('http://www.youtu.be') === 0 || dataValue.indexOf('http://youtube.com') === 0 || dataValue.indexOf('http://youtu.be') === 0 ) {
                    interaction.object.attr('type', 'video/youtube');
                }

                reRenderMediaInteraction(interaction);
            }
        }, 1000);
        
        
        
        //and so on for the other attributes...
        
        formElement.initDataBinding($form, interaction, callbacks, {invalidate:true});
        
        //_widget.on('attributeChange', function(data){
            //if the template changes, forward the modification to a helper
            //answerStateHelper.forward(_widget);
        //});
         
         
        var selectMediaButton = $(_widget.$form).find(".selectMediaFile");
        selectMediaButton.on('click', function() {
            
            $(this).resourcemgr({
                appendContainer : options.mediaManager.appendContainer,
                root : '/',
                browseUrl : options.mediaManager.browseUrl,
                uploadUrl : options.mediaManager.uploadUrl,
                deleteUrl : options.mediaManager.deleteUrl,
                downloadUrl : options.mediaManager.downloadUrl,
                params : {
                    uri : options.uri,
                    lang : options.lang,
                    filters : 'video/mp4,video/avi,video/ogv,video/mpeg,video/ogg,video/quicktime,video/webm,video/x-ms-wmv,video/x-flv,audio/mp3,audio/vnd.wav,audio/ogg,audio/vorbis,audio/webm,audio/mpeg'
                },
                pathParam : 'path',
                select : function(e, files){
                    if(files.length > 0){ 
                        // set data field content and meybe detect and set media type here
                        var dataInput = $($form.find('input[name=data]'));
                        dataInput.val( files[0].file );
                        interaction.object.attr('type', files[0].mime);
                        dataInput.trigger('change');
                    }
                }
            });
        });
        
    };

    return MediaInteractionStateQuestion;
});
