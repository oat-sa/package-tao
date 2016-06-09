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
                <mapResponse identifier="{{responseIdentifier}}" />
            </sum>
        </setOutcomeValue>
    </responseIf>
</responseCondition>