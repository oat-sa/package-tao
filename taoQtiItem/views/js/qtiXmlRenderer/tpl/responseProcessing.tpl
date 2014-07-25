{{#if custom ~}}
    {{{xml}}}
{{~/if ~}}

{{~#if template ~}}
    <responseProcessing template="{{template}}"/>
{{~/if ~}}

{{~#if templateDriven ~}}
<responseProcessing>
    {{#responseRules}}{{{.}}}{{/responseRules}}
    {{#feedbackRules}}{{{.}}}{{/feedbackRules}}
</responseProcessing>
{{~/if ~}}