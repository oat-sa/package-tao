<div class="media-sizer media-sizer-synced">

    <label>
        <input type="checkbox" checked="checked" class="media-mode-switch"/>
        <span class="icon-checkbox"></span>
        {{__ 'Responsive mode'}}
    </label>
    <span class="icon-help tooltipstered" data-tooltip="~ .tooltip-content:first" data-tooltip-theme="info"></span>

    <div class="tooltip-content">{{__ 'The image resizes along with its container.'}}</div>


    <div class="media-sizer-percent">
        <label for="">{{__ 'Size'}}</label>
                <span class="item-editor-unit-input-box">
                    <input type="text" name="width" value="" data-validate="$numeric"
                           data-validate-option="$allowEmpty;"/>
                </span>

        <div class="media-sizer-slider-box">
            <div class="media-sizer-slider"></div>
        </div>
    </div>

    <div class="media-sizer-pixel" style="display:block; margin-top: 20px">
        <label for="">{{__ 'Width'}}</label>
                <span class="item-editor-unit-input-box">
                    <input type="text" name="width" value="" data-validate="$numeric"
                           data-validate-option="$allowEmpty;"/>
                </span>

        <label for="">{{__ 'Height'}}</label>
                <span class="item-editor-unit-input-box">
                    <input type="text" name="height" value="" data-validate="$numeric"
                           data-validate-option="$allowEmpty;"/>
                </span>

        <div class="media-sizer-link">
            <span class="icon-link"></span>
        </div>

        <div class="media-sizer-slider-box">
            <div class="media-sizer-slider"></div>
            <div class="media-sizer-cover"></div>
        </div>
    </div>
</div>