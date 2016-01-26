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
<hr>
<div class="panel">
    <h3 class="full-width">{{__ "Contraints"}}</h3>
    <select name="constraint" class="select2" data-has-search="false">
        {{#each constraints}}
            <option value="{{@key}}" {{#if selected}}selected="selected"{{/if}}>{{label}}</option>
        {{/each}}
    </select>
</div>
<div class="panel extendedText">
    {{!-- Let the user enter his own pattern --}}
    <div id="constraint-pattern" {{#unless constraints.pattern.selected}}style="display:none"{{/unless}}>
        <label>
            {{__ "pattern"}}
        </label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <span class="tooltip-content">{{__ "If given, the pattern mask specifies a regular expression that the candidate's response must match in order to be considered valid"}}</span>
        <input type="text" name="patternMask" value="{{#if patternMask}}{{patternMask}}{{/if}}"/>
    </div>
    {{!-- Use the patternMask w/ a regex controlled by thoses UI components --}}
    <div id="constraint-maxLength" {{#unless constraints.maxLength.selected}}style="display:none"{{/unless}}>
        <label class="spinner">
            {{__ "Max length"}}
        </label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <span class="tooltip-content">{{__ "We will use the patternMask to do this, to be compliant with the IMS standard"}}</span>
        <input type="text" data-min="0" data-increment="1" class="incrementer" name="maxLength" {{#if maxLength}}value="{{maxLength}}"{{/if}} />
    </div>
    {{!-- Use the patternMask w/ a regex controlled by thoses UI components --}}
    <div id="constraint-maxWords" {{#unless constraints.maxWords.selected}}style="display:none"{{/unless}}>
        <label class="spinner">
            {{__ "Max words"}}
        </label>
        <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
        <span class="tooltip-content">{{__ "We will use the patternMask to do this, to be compliant with the IMS standard"}}</span>
        <input type="text" data-min="0" data-increment="1" class="incrementer" name="maxWords" {{#if maxWords}}value="{{maxWords}}"{{/if}}/>
    </div>
</div>
<hr>
<div class="panel extendedText">
    <h3 class="full-width">{{__ "Recommendations"}}</h3>
    <label class="spinner">
        {{__ "Length"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "Provides a hint to the candidate as to the expected overall length of the desired response measured in number of characters."}}</span>
    <input type="text" data-min="0" data-increment="1" class="incrementer" name="expectedLength" value="{{#if expectedLength}}{{expectedLength}}{{/if}}"/>
    <label for="" class="spinner">
        {{__ "Lines"}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
    <span class="tooltip-content">{{__ "Provides a hint to the candidate as to the expected number of lines of input required. A line is expected to have about 72 characters."}}</span>
    <input type="text" class="incrementer" data-min="0" data-increment="1" name="expectedLines" value="{{#if expectedLines}}{{expectedLines}}{{/if}}">
</div>
