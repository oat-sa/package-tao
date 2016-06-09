<span class="cascading-combo-box">
    {{#if comboboxLabel}}<label>{{comboboxLabel}}</label>{{/if}}
    <select class="" data-id="{{comboboxId}}" data-has-search="false">
        <option></option>{{!-- select2 needs an empty option for the placeholder --}}
        {{#each options}}
        <option value="{{id}}" data-categories="{{categories}}">{{label}}</option>
        {{/each}}
    </select>
</span>