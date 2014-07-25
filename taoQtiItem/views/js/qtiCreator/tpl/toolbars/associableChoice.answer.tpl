<div class="mini-tlb" data-edit="answer" data-for="{{choiceSerial}}">
    <label class="tlb-button right" title="This answer is correct" data-edit="correct">
        <input 
            type="checkbox" 
            data-role="correct"
            name="correct_{{interactionSerial}}[]" 
            value="{{choiceIdentifier}}" 
            {{#if correct}}checked="checked"{{/if}}/>
        <span class="icon-checkbox"></span>
    </label>
    <label class="tlb-button right" title="Score of this answer" data-edit="map">
        <input value="{{score}}" type="text" data-role="score" data-for="{{choiceIdentifier}}" name="score" class="score" data-validate="$numeric" data-validate-option="$allowEmpty; $event(type=keyup)" />
    </label>
</div>