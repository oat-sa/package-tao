<div class="grid-row">
    <div class="col-4">
       {{{left}}}
    </div>
    <div class="col-3">
       {{{right}}}
    </div>
    
    <div class="col-2" data-edit="correct" {{#if defineCorrect}}style="display:block"{{/if}}>
        <label>
            <input name="{{id}}-correct" type="checkbox" {{#if correct}} checked="checked"{{/if}} />
            <span class="icon-checkbox"></span>
        </label>
    </div>

    <div class="col-2">
        <input type="text" 
               value="{{score}}" 
               name="{{id}}-score" 
               class="score" 
               {{#if defaultScore}}data-default="true"{{/if}}  
               data-validate="$numeric" 
               data-validate-option="$allowEmpty; $event(type=keyup)" 
        />
    </div>
    <div class="col-1">
        <a href="#" class="pair-deleter" id="{{id}}-delete" title="{{__ 'Remove pair'}}"><span class="icon-bin"></span></a>
    </div>
</div>
