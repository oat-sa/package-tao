{{#if block}}
<span data-serial="{{serial}}" data-qti-class="math">
    <math display = "block">{{{raw}}}</math>
</span>
{{else}}
<span data-serial="{{serial}}" data-qti-class="math">
    <math>{{{raw}}}</math>
</span>   
{{/if}}