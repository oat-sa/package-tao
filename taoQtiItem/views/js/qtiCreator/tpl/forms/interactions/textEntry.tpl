<div class="panel">
    <label for="placeholderText" class="spinner">Placeholder Text</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <input name="placeholderText" value="{{placeholderText}}" type="text" />
    <span class="tooltip-content">
        {{__ "In visual environments, string interactions are typically represented by empty boxes into which the candidate writes or types.  Delivery engines should use the value of this attribute (if provided) instead of their default placeholder text when this is required."}}
    </span>
</div>
<div class="panel">
    <label for="patternMask" class="spinner">Pattern Mask</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <input name="patternMask" value="{{patternMask}}" type="text" data-validate="$validRegex;" placeholder="e.g. [A-Z][a-z]{3,}"/>
    <span class="tooltip-content">
        {{__ "If given, the pattern mask specifies a regular expression that the candidate's response must match in order to be considered valid.Care is needed to ensure that the format of the required input is clear to the candidate, especially when validity checking of responses is required for progression through a test. This could be done by providing an illustrative sample response in the prompt, for example."}}
    </span>
</div>
<div class="panel">
    <label for="expectedLength" class="spinner">Expected Length</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <input name="expectedLength" value="{{expectedLength}}" data-increment="1" data-min="1" data-max="100" type="text" />
    <span class="tooltip-content">
        {{__ "The expectedLength attribute provides a hint to the candidate as to the expected overall length of the desired response measured in number of characters. This is not a validity constraint."}}
    </span>
</div>
<div class="panel">
    <label for="base" class="spinner">Base</label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content" data-tooltip-theme="info"></span>
    <input name="base" value="{{base}}" data-increment="1" data-min="1" data-max="100" type="text" />
    <span class="tooltip-content">
        {{__ "If the string interaction is bound to a numeric response variable then the base attribute must be used to set the number base in which to interpret the value entered by the candidate."}}
    </span>
</div>
