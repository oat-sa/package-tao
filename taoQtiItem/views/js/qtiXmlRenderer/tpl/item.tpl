<?xml version="1.0" encoding="UTF-8"?>
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1  http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd"
    {{~#each namespaces}} {{#if @key}}xmlns:{{@key}}="{{.}}"{{/if}}{{/each}}
    {{#if attributes}}{{{join attributes '=' ' ' '"'}}}{{/if}}>
    
    {{~#responses}}
        {{{.}}}
    {{~/responses}}
    {{~#outcomes}}
        {{{.}}}
    {{~/outcomes}}
    {{~#stylesheets}}
        {{{.}}}
    {{~/stylesheets}}
    
    <itemBody>
        {{#if empty}}
            <div class="empty"></div>
        {{else}}
            {{{body}}}
        {{/if}}
    </itemBody>
    
    {{{responseProcessing}}}
    
    {{~#feedbacks}}{{{.}}}{{/feedbacks}}
</assessmentItem>