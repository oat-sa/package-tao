{{#each buttons}}
    <button class="btn-{{type}} small {{id}}" data-control="{{id}}" type="button">{{#if icon}}<span class="icon-{{icon}}"></span> {{/if}}{{label}}</button>
{{/each}}
