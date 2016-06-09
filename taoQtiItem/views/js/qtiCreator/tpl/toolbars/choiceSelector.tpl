<div class="choices-selector">
    <select {{#if multiple}}multiple="multiple"{{/if}} placeholder="{{__ "Enter choices…"}}" data-has-search="false">
    {{#each options}}
    <option value="{{value}}" title="{{title}}" {{#if selected}}selected="selected"{{/if}}>{{label}}</option>
    {{/each}}>

    </select>
</div>
