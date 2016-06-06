<?xml version="1.0" encoding="UTF-8"?>
<assessmentItem 
    {{#each namespaces}}{{#if @key}}xmlns:{{@key}}="{{.}}"{{else}}xmlns="{{.}}"{{/if}} {{/each}}
    {{xsi}}schemaLocation="{{schemaLocations}}"
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
    
    <itemBody{{#if class}} class="{{class}}"{{/if}}>
        {{#if empty}}
            <div class="empty"></div>
        {{else}}
            {{{body}}}
        {{/if}}
    </itemBody>
    
    {{{responseProcessing}}}
    
    {{~#feedbacks}}{{{.}}}{{/feedbacks}}
    
    {{{apipAccessibility}}}
</assessmentItem>
