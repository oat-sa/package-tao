<div {{#if attributes.id}}id="{{attributes.id}}"{{/if}} class="qti-interaction qti-blockInteraction qti-matchInteraction{{#if attributes.class}} {{attributes.class}}{{/if}}" data-serial="{{serial}}" data-qti-class="matchInteraction">
  {{#if prompt}}{{{prompt}}}{{/if}}
  <div class="instruction-container"></div>
  <div class="match-interaction-area">
    <table class="matrix">
      <thead>
      <tr>
        <th> </th>
        {{#matchSet1}}{{{.}}}{{/matchSet1}}
      </tr>
      </thead>
      <tbody>
      {{#matchSet2}}
      <tr>
        {{{.}}}
        {{#each ../matchSet1}}
        <td>
          <label>
            <input type="checkbox" >
            <span class="icon-checkbox cross"></span>
          </label>
        </td>
        {{/each}}
      </tr>
      {{/matchSet2}}
      </tbody>
    </table>
  </div>
  <div class="notification-container"></div>
</div>
