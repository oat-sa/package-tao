<div class="panel">
    <label for="format" class="spinner">{{__ "Format"}}</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "Used to control the format of the text entered by the candidate."}}</span>
    <select name="format" class="select2" data-has-search="false">
    	{{#each formats}}
    		<option value="{{@key}}" {{#if selected}}selected="selected"{{/if}}>{{label}}</option>
    	{{/each}}
    </select>
</div>