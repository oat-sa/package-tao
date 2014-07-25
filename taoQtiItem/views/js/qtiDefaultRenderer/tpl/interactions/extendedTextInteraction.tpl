<div class="qti_widget qti_{{_type}} {{attributes.class}}">
    {{#if prompt}}{{{prompt}}}{{/if}}
    {{#if multiple}}
    <div id="{{attributes.identifier}}">
        {{#maxStringLoop}}<input id="{{attributes.identifier}}_{{.}}" name="{{attributes.identifier}}_{{.}}"/><br />{{/maxStringLoop}}
    </div>
    {{else}}
    <textarea id="{{attributes.identifier}}" name="{{attributes.identifier}}"></textarea>
    {{/if}}
</div>