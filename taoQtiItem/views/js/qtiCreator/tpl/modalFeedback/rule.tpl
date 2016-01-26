<div class="feedbackRule-container" data-serial="{{serial}}">
    <div class="feedbackRule-rule-if">
        <span class="feedbackRule-desc i">if</span>
        <select class="feedbackRule-condition select2" data-has-search="false">
            {{#each availableConditions}}
            <option value="{{name}}" {{#equal name ../condition}}selected="selected"{{/equal}}>{{label}}</option>
            {{/each}}
        </select>
        <input class="feedbackRule-compared-value score" type="text" value="{{comparedValue}}" {{#if hideScore}}style="display:none"{{/if}}/>
    </div>
    <div class="feedbackRule-then-else">
        <span class="feedbackRule-desc i">then</span>
        <button class="btn-info small" type="button" data-feedback="then">{{__ 'Feedback'}}</button>
    </div>
    {{#if addElse}}
    <a title="{{__ 'Add else feedback'}}" href="#" class="adder feedbackRule-add-else">else</a>
    {{/if}}
    {{#if feedbackElse}}
     <div class="feedbackRule-then-else">
            <span class="feedbackRule-desc i">else</span>
            <button class="btn-info small" type="button" data-feedback="else">{{__ 'Feedback'}}</button>
            <span class="feedbackRule-button-delete icon-bin" title="{{__ "Delete else statement"}}" data-role="else"></span>
        </div>
    {{/if}}
    <span class="feedbackRule-button-delete icon-bin" title="{{__ "Delete this modal feedback"}}" data-role="rule"></span>
</div>