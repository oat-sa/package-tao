{{#unless noData}}
<responseCondition>
    <responseIf>
        <match>
            <variable identifier="{{response}}" />
            {{#if multiple}}
            <multiple>
                {{#each choices}}
                <baseValue baseType="identifier">{{.}}</baseValue>
                {{/each}}
            </multiple>
            {{else}}
            <baseValue baseType="identifier">{{choice}}</baseValue>
            {{/if}}
        </match>
        <setOutcomeValue identifier="{{feedback.outcome}}">
            <baseValue baseType="identifier">{{feedback.then}}</baseValue>
        </setOutcomeValue>
    </responseIf>
    {{~#if feedback.else}}
    <responseElse>
        <setOutcomeValue identifier="{{feedback.outcome}}">
            <baseValue baseType="identifier">{{feedback.else}}</baseValue>
        </setOutcomeValue>
    </responseElse>
    {{~/if}}
</responseCondition>
{{/unless}}