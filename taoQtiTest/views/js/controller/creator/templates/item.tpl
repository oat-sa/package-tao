{{#each .}}
     <li data-uri='{{uri}}' class='truncate'>
        {{label}} 
        {{#if parent}}<span class='flag truncate' title="{{parent}}">{{parent}}</span>{{/if}}
     </li>
{{/each}}
