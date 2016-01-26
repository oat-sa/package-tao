{{#each list}}
<tr data-id="{{id}}">
    {{#if ../selectable}}
    <td class="checkboxes"><input type="checkbox" name="cb[{{id}}]" value="1" /></td>
    {{/if}}
    <td class="label">{{label}}</td>
    {{#if ../actions}}
    <td class="actions">
        {{#each ../../actions}}
            {{#with ../../line}}
                {{#unless ../hidden}}
                    {{#with ../../this}}
        <button class="btn-info small" data-control="{{id}}"{{#if title}} title="{{title}}"{{/if}}>
            {{#if icon}}<span class="icon icon-{{icon}}"></span>{{/if}}
            {{label}}
        </button>
                    {{/with}}
                {{/unless}}
            {{/with}}
        {{/each}}
    </td>
    {{/if}}
</tr>
{{/each}}
