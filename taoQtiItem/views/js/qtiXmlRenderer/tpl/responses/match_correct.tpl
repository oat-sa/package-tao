<responseCondition>
    <responseIf>
        <match>
            <variable identifier="{{responseIdentifier}}" />
            <correct identifier="{{responseIdentifier}}" />
        </match>
        <setOutcomeValue identifier="{{outcomeIdentifier}}">
            <sum>
                <variable identifier="{{outcomeIdentifier}}" />
                <baseValue baseType="integer">1</baseValue>
            </sum>
        </setOutcomeValue>
    </responseIf>
</responseCondition>