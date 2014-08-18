<h3>{{__ "MIME-type"}}</h3>
<span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
<div class="tooltip-content">{{__ "The MIME-type attribute describes which kind of file may be uploaded."}}</div>

<div class="reset-group">
    <select name="type" class="select2" data-has-search="false">
    	{{#each types}}
    		<option value="{{mime}}" {{#if selected}}selected="selected"{{/if}}>{{label}}</option>
    	{{/each}}
    </select>
</div>
