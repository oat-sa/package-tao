<div class="widget-box widget-inline widget-inlineChoiceInteraction" data-serial="{{serial}}" data-edit="active">
    <div class="qti-interaction qti-inlineChoiceInteraction">
        <table>
            <colgroup>
                <col class="text">
                <col class="icon">
                <col class="icon">
            </colgroup>
            <tbody>
                <tr>
                    <td class="main-option"><div>-- {{__ "edit choices"}} --<span class="icon-down"></span></div></td>
                    <td class="icon-title" data-edit="map">correct</td>
                    <td class="icon-title" data-edit="map">score</td>
                </tr>
                {{#choices}}{{{.}}}{{/choices}}
                <tr data-edit="question">
                    <td>
                        <div class="add-option">
                            <span class="icon-add"></span>
                            {{__ "Add choice"}}
                        </div>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>
        <div>
            <div class="widget-response" data-edit="correct"></div>
            <div class="padding"></div>
        </div>
    </div>
</div>