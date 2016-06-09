<aside class="action-bar {{#if vertical}}vertical-action-bar{{else}}horizontal-action-bar{{/if}} clearfix">
    {{#each buttons}}
    <button class="btn-info small {{#if conditional}} conditional hidden{{/if}}" data-control="{{id}}" {{#if title}} title="{{title}}"{{/if}}>
        {{#if icon}}<span class="icon icon-{{icon}}"></span>{{/if}}
        {{label}}
    </button>
    {{/each}}
</aside>
