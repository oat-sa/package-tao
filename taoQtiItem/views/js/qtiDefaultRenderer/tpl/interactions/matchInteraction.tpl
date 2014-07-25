<div id="{{attributes.identifier}}" class="qti_widget qti_{{_type}} {{attributes.class}}">
    <div class = "qti_{{_type}}_container" >
        {{#if prompt}}{{{prompt}}}{{/if}}
        <ul class="choice_list">
            {{#matchSet1}}{{{.}}}{{/matchSet1}}
        </ul>
        <ul class="choice_list">
            {{#matchSet2}}{{{.}}}{{/matchSet2}}
        </ul>
    </div>
</div>