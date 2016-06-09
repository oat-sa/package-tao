<div class="widget-box widget-inline widget-textEntryInteraction qti-interaction" data-serial="{{serial}}" data-edit="active">
    <div class="qti-interaction qti-textEntryInteraction">
        <table>
            <colgroup>
                <col class="text">
                <col class="icon">
                <col class="icon">
            </colgroup>
            <tbody>
                <tr data-edit="question">
                    <td class="main-option"></td>
                    <td colspan="2"></td>
                </tr>
                <tr data-edit="correct">
                    <td data-text>
                        <div class="instruction-container"></div>
                        <input type="text" name="correct" value="{{text}}" /></td>
                    <td class="mini-tlb" colspan="2">
                    </td>
                </tr>
                <tr data-add-option data-edit="map">
                    <td>
                        <div class="add-option">
                            <span class="icon-add"></span>
                            {{__ "Add another option"}}
                        </div>
                    </td>
                    <td colspan="2"></td>
                </tr>
                <tr data-edit="custom">
                    <td colspan="3">
                        <!-- Input solely provides visual in response state -->
                        <input type="text" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>