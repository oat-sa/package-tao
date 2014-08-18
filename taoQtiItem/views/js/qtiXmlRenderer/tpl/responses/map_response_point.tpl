<responseCondition>
    <responseIf>
        <not>
            <isNull>
                <variable identifier="{{responseIdentifier}}" />
            </isNull>
        </not>
        <setOutcomeValue identifier="{{outcomeIdentifier}}">
            <sum>
                <variable identifier="{{outcomeIdentifier}}" />
                <mapResponsePoint identifier="{{responseIdentifier}}" />
            </sum>
        </setOutcomeValue>
    </responseIf>
</responseCondition>