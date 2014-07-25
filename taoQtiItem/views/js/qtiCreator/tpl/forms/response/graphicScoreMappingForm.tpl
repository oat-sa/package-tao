    <a class="closer" href="#" data-close=":parent .mapping-editor"></a>
    <div class="form-container">
        <h2>{{identifier}}</h2>

{{#unless noCorrect}}
        <div class="panel" data-edit="correct">
            <label>
                {{__ "Correct"}}
                <input name="correct" type="checkbox" {{#if correct}} checked="checked"{{/if}} />
                <span class="icon-checkbox"></span>
            </label>
            <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
            <span class="tooltip-content">{{__ 'Is this choice the correct response?'}}</span>
        </div>
{{/unless}}
        <div class="panel">
            <label for="score">{{__ "Score"}}</label>
            <input value="{{score}}" type="text" data-for="{{identifier}}" name="score" class="score" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)" />
            <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
            <span class="tooltip-content">{{__ 'Set the score for this response'}}</span>
        </div>
        <span class="arrow"></span>
        <span class="arrow-cover"></span>
    </div>
