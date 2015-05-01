<select class="qti-interaction qti-inlineInteraction qti-inlineChoiceInteraction"
        data-serial="{{serial}}"
        data-qti-class="inlineChoiceInteraction"
        data-has-search="false">
    <option></option>                {{!-- select2 needs an empty option for the placeholder --}}
    <option value="empty"></option>
    {{#choices}}{{{.}}}{{/choices}}
</select>
