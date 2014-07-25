<div id="qtiAuthoring_mapping_container"></div>

<div id="qtiAuthoring_response_container"></div>

<div id="qtiAuthoring_response_title" class="ui-widget-header ui-corner-top ui-state-default">
    <span id="qtiAuthoring_response_title_text"><?=__('Response editor')?></span>
    <span id="qtiAuthoring_response_title_fixed">fixed<input type="checkbox" value="fixed"/></span>
</div>

<div id="qtiAuthoring_responseEditor" class="ui-widget-content ui-corner-bottom">
    <div id="qtiAuthoring_response_formContainer" class="ext-home-container"></div>
    <div id="qtiAuthoring_response_gridContainer" class="ext-home-container">
        <?if(has_data('response_grid_tip')):?><span class="ui-icon ui-icon-alert"></span><p class="qti-form-tip"><?=get_data('response_grid_tip')?></p><?endif;?>
        <table id="qtiAuthoring_response_grid"></table>
    </div>
    <div id="qtiAuthoring_feedback_formContainer" class="ext-home-container">
        <div id="qtiAuthoring_feedback_title" class="ui-widget-header ui-corner-top ui-state-default"><?=__('Feedbacks')?></div>
        <div id="qtiAuthoring_feedback_content" class="ui-widget-content ui-corner-bottom">
            <div id="qtiAuthoring_feedback_list"></div>
            <div id="add_feedback_button" class="add_feedback_button">
                <a href="#"><img src="<?=ROOT_URL?>tao/views/img/add.png"> <?=__('Add modal feedback')?></a>
            </div>
        </div>
    </div>
</div>