<input value="{{score}}" 
       title="Score of this answer" 
       type="text" 
       data-for="{{choiceIdentifier}}" 
       name="score" 
       class="score" 
       placeholder = "{{placeholder}}"
       data-validate="$numeric" 
       data-validate-option="$allowEmpty; $event(type=keyup)" />