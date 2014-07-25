<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    <ul class="qti_choice_list">
        {{#choices}}{{{.}}}{{/choices}}
    </ul>
</div>