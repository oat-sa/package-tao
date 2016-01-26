<li class="qti-choice qti-simpleChoice" data-identifier="{{attributes.identifier}}" data-serial="{{serial}}">
    <div class="pseudo-label-box">
        <label class="real-label">
            {{#if unique}}
            <input type="radio" name="response-{{interaction.serial}}" value="{{attributes.identifier}}" tabindex="1">
            <span class="icon-radio"></span>
            {{else}}
            <input type="checkbox" name="response-{{interaction.serial}}" value="{{attributes.identifier}}" tabindex="1">
            <span class="icon-checkbox"></span>
            {{/if}}
        </label>
        <div class="label-box">
            <div class="label-content clear" contenteditable="false">
                {{{body}}}
            </div>
        </div>
    </div>
</li>
