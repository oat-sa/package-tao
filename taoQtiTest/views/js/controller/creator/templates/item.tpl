{{#each .}}
     <li data-uri='{{uri}}'>
        {{label}} 
        {{#if parent}}<span class='flag truncate' title="{{parent}}">{{parent}}</span>{{/if}}
     </li>
{{/each}}
