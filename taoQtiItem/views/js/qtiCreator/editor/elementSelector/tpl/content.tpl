<div class="elementSelector">

    <ul class="group-list plain">
        {{#each groups}}
        <li data-group-name="{{name}}"><a href="#" class="group-selector">{{label}}</a></li>
        {{/each}}
    </ul>

    <div class="element-group-container">
        {{#each groups}}
        <div class="element-group" data-group-name="{{name}}">
            <ul class="element-list plain">
                {{#each elements}}
                <li data-qti-class="{{qtiClass}}" title="{{title}}">
                    {{#if iconFont}}
                    <span class="icon {{icon}}"></span>
                    {{else}}
                    <img class="icon" src="{{icon}}"/>
                    {{/if}}
                    <span class="label truncate">{{label}}</span>
                </li>
                {{/each}}
            </ul>
        </div>
        {{/each}}
    </div>

</div>