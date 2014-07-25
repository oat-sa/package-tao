Qti.DefaultRenderer = Qti.Renderer.extend({
    getRawTemplates : function(){
        return {
            '#assessmentItem' : ['<div id="{{attributes.identifier}}" class="qti_item">',
                '<h1>{{attributes.title}}</h1>',
                '<div class="qti_item_body">{{{body}}}</div>',
                '</div>'],
            '#prompt' : '<p class="prompt">{{{body}}}</p>',
            '#interaction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<ul class="qti_choice_list">',
                '{{#choices}}{{{.}}}{{/choices}}',
                '</ul>',
                '</div>'],
            '#choiceInteraction' : '#interaction',
            '#inlineChoiceInteraction' : [
                '<select id="{{attributes.identifier}}" name="{{attributes.identifier}}" class="qti_{{_type}} {{attributes.class}}">',
                '{{#choices}}{{{.}}}{{/choices}}',
                '</select>'],
            '#orderInteraction' : '#interaction',
            '#associateInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '<div class = "qti_{{_type}}_container" >',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<ul class="qti_choice_list">',
                '{{#choices}}{{{.}}}{{/choices}}',
                '</ul>',
                '{{#body}}<div>{{{body}}}</div>{{/body}}',
                '</div>',
                '</div>'],
            '#textEntryInteraction' : '<input type="text" id="{{attributes.identifier}}" name="{{attributes.identifier}}" class="qti_{{_type}} {{attributes.class}}" />',
            '#extendedTextInteraction' : [
                '<div class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '{{#multiple}}',
                '<div id="{{attributes.identifier}}">',
                '{{#maxStringLoop}}<input id="{{attributes.identifier}}_{{.}}" name="{{attributes.identifier}}_{{.}}"/><br />{{/maxStringLoop}}',
                '</div>',
                '{{/multiple}}',
                '{{^multiple}}',
                '<textarea id="{{attributes.identifier}}" name="{{attributes.identifier}}"></textarea>',
                '{{/multiple}}',
                '</div>'],
            '#matchInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '<div class = "qti_{{_type}}_container" >',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<ul class="choice_list">',
                '{{#matchSet1}}{{{.}}}{{/matchSet1}}',
                '</ul>',
                '<ul class="choice_list">',
                '{{#matchSet2}}{{{.}}}{{/matchSet2}}',
                '</ul>',
                '</div>',
                '</div>'],
            '#gapMatchInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '<div class = "qti_{{_type}}_container" >',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<ul class="qti_choice_list">',
                '{{#choices}}{{{.}}}{{/choices}}',
                '</ul>',
                '<div class="qti_flow_container">{{{body}}}</div>',
                '</div>',
                '</div>'],
            '#hottextInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<div class="qti_flow_container">{{{body}}}</div>',
                '</div>'],
            '#graphicInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '</div>'],
            '#selectPointInteraction' : '#graphicInteraction',
            '#hotspotInteraction' : '#graphicInteraction',
            '#graphicOrderInteraction' : '#graphicInteraction',
            '#graphicAssociateInteraction' : '#graphicInteraction',
            '#graphicGapMatchInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{#prompt}}{{{prompt}}}{{/prompt}}',
                '<ul class="qti_graphic_gap_match_spotlist">',
                '{{#gapImgs}}{{{.}}}{{/gapImgs}}',
                '</ul>',
                '</div>'],
            '#sliderInteraction' : '#graphicInteraction',
            '#mediaInteraction' : [
                '<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">',
                '{{{prompt}}}',
                '{{{media}}}',
                '</div>'],
            '#choice' : '<li id="{{attributes.identifier}}" class="{{classes}}">{{{body}}}</li>',
            '#simpleChoice' : '#choice',
            '#simpleAssociableChoice' : '#choice',
            '#hotspotChoice' : '#choice',
            '#associableHotspot' : '#choice',
            '#gapText' : '#choice',
            '#gap' : '<span class="gap" id="{{attributes.identifier}}"></span>',
            '#gapImg' : ['<li id="{{attributes.identifier}}">',
                '<img src="{{object.attributes.data}}"  width="{{object.attributes.width}}" height="{{object.attributes.height}}" alt="{{object.attributes._alt}}"/>',
                '</li>'],
            '#hottext' : '<span class="hottext_choice hottext_choice_off {{attributes.class}}" id="hottext_choice_{{attributes.identifier}}">{{{body}}}</span>',
            '#inlineChoice' : '<option value="{{attributes.identifier}}" class="{{attributes.class}}">{{{body}}}</option>',
            '#object' : [
                '{{#video}}<video id="{{serial}}" src="{{attributes.data}}" type="{{attributes.type}}" {{#attributes.width}}width="{{attributes.width}}"{{/attributes.width}} {{#attributes.height}}height="{{attributes.height}}{{/attributes.height}}" controls="controls" preload="none"></video>{{/video}}',
                '{{#audio}}<audio id="{{serial}}" src="{{attributes.data}}" type="{{attributes.type}}"></audio>{{/audio}}',
                '{{#object}}<object id="{{serial}}" src="{{attributes.data}}" type="{{attributes.type}}" width="{{attributes.width}}" height="{{attributes.height}}">{{attributes.alt}}</object>{{/object}}',
            ],
            '#math' : '<math id="{{serial}}" {{#block}}display = "block"{{/block}}>{{{body}}}</math>',
            '#modalFeedback' : '<div id="{{serial}}" title="{{attributes.title}}" class="qti-modal-feedback">{{{body}}}</div>'
        }
    },
    getPostRenderers : function(){
        return {
            '#interaction' : function(interaction, data){

                var wwwPath = '';
                //use the global variable qti_base_www
                if(typeof(qti_base_www) !== 'undefined'){
                    wwwPath = qti_base_www;
                    if(!/\/$/.test(wwwPath) && wwwPath != ''){
                        wwwPath += '/';
                    }
                }
                
                var pluginPath = '';
                if(typeof(qti_plugin_path) !== 'undefined'){
                    pluginPath = qti_plugin_path;
                }
                
                var graphicDebug = false;
                if(typeof(qti_debug) !== 'undefined'){
                    graphicDebug = qti_debug;
                }

                var interactionName = interaction.qtiTag.charAt(0).toUpperCase() + interaction.qtiTag.slice(1);
                if(typeof(qti_debug) !== 'undefined'){
                    if(!QtiWidget || !QtiWidget.DefaultWidget || !QtiWidget.DefaultWidget[interactionName]){
                        alert("Error: Unknow widget " + interactionName);
                    }
                }

                var context = {
                    'wwwPath' : wwwPath,
                    'graphicDebug' : graphicDebug,
                    'pluginPath': pluginPath
                };
                interaction.widget = new QtiWidget.DefaultWidget[interactionName](interaction, context);
                interaction.widget.render();

            },
            '#associateInteraction' : '#interaction',
            '#choiceInteraction' : '#interaction',
            '#endAttemptInteraction' : '#interaction',
            '#extendedTextInteraction' : '#interaction',
            '#gapMatchInteraction' : '#interaction',
            '#graphicAssociateInteraction' : '#interaction',
            '#graphicGapMatchInteraction' : '#interaction',
            '#graphicOrderInteraction' : '#interaction',
            '#hotspotInteraction' : '#interaction',
            '#hottextInteraction' : '#interaction',
            '#inlineChoiceInteraction' : '#interaction',
            '#matchInteraction' : '#interaction',
            '#mediaInteraction' : '#interaction',
            '#orderInteraction' : '#interaction',
            '#selectPointInteraction' : '#interaction',
            '#sliderInteraction' : '#interaction',
            '#textEntryInteraction' : '#interaction',
            '#uploadInteraction' : '#interaction',
            '#object' : function(object, data){
                
                var pluginPath = '';
                if(typeof(qti_plugin_path) !== 'undefined'){
                    pluginPath = qti_plugin_path;
                }
                
                var context = {
                    'pluginPath' : pluginPath
                }
                object.widget = new QtiWidget.DefaultWidget.Object(object, context);
                object.widget.render();
            },
            '#math' : function(math, data){
                var $mathElt = $('#' + math.serial);
                if(typeof(MathJax) !== 'undefined' && MathJax){
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub, $mathElt.parent()[0]]);
                }
            },
            '#modalFeedback' : function(feedback, data){
                $('#' + feedback.getSerial()).dialog({
                    modal : true,
                    width : 400,
                    height : 300,
                    buttons : [
                        {
                            text : 'Close',
                            click : function(){
                                $(this).dialog('close');
                            }
                        }
                    ],
                    open:function(){
                        feedback.getBody().postRender(feedback.getRenderer());
                    },
                    close : function(){
                        $(this).empty();
                        if(typeof(data.callback) === 'function'){
                            data.callback();
                        }
                    }
                });
            }
        }
    },
    setResponse : function(interaction, response){
        if(response !== null && typeof(response) !== 'undefined'){
            return interaction.widget.setResponse(response);
        }
    },
    getResponse : function(interaction){
        return interaction.widget.getResponse();
    }
});

