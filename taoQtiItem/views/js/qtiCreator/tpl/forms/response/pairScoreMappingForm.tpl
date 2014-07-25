    <div class="form-container">
        <h1>{{title}}</h1>

        <hr />
        <div class="panel grid-row">
            <h2>{{__ "Add new pairs"}}:</h2>
        </div> 
        <div class="panel grid-row">
            <div class="col-4">
                <select class="select2 new-pair-left" data-placeholder="{{__ 'Select a '}}{{leftTitle}}">
                    <option></option>
                    {{#each pairLeft}}
                    <option value="{{id}}">{{{value}}}</option> 
                    {{/each}}
                </select>
             </div>
             <div class="col-4">
                <select class="select2 new-pair-right"  data-placeholder="{{__ 'Select a '}}{{rightTitle}}">
                    <option></option>
                    {{#each pairRight}}
                    <option value="{{id}}">{{{value}}}</option> 
                    {{/each}}
                </select>
             </div>
             <div class="col-1">
                &nbsp;
            </div>
             <div class="col-2">
                <button class="pair-adder btn-info small"><span class="icon-add"></span>{{__ 'Add'}}</button>
            </div> 
        </div>
        <hr />
        <div class="panel grid-row heading">
             <div class="col-4">{{leftTitle}}</div>
             <div class="col-3">{{rightTitle}}</div>
            
            <div class="col-2" data-edit="correct" {{#if defineCorrect}}style="display:block"{{/if}}>
                <span>{{__ "Correct"}}</span>
                <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
                <span class="tooltip-content">{{__ 'Is this pair the correct response?'}}</span>
            </div>

            <div class="col-2">
                <span>{{__ "Score"}}</span>
                <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>
                <span class="tooltip-content">{{__ 'Set the score for this response'}}</span>
            </div>
            <div class="col-1"></div>
        </div>

        <div class="panel pairs">
        {{#each pairs}}
            {{{.}}}
        {{/each}}
        </div>

        <hr />

        <span class="arrow"></span>
        <span class="arrow-cover"></span>
    </div>
