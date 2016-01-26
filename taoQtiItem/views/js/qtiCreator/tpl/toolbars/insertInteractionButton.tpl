<li
{{#if subGroup}}
    data-sub-group="{{subGroup}}"
{{/if}}
    data-qti-class="{{qtiClass}}"

{{#if disabled}}
    class="disabled"
    title="element available in the final release"
{{else}}
    title="{{title}}"
{{/if}}
>
    {{#if iconFont}}
    <span class="icon {{icon}}"></span>
    {{else}}
    <img class="icon" src="{{icon}}">
    {{/if}}
    
    <div class="truncate">{{short}}</div>
</li>