<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?=get_data('title')?></title>

        <!-- CSS -->
        <?if(DEBUG_MODE):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qti.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/normalize.css" media="screen" />
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>../../css/base.css" media="screen" />
        <?else:?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>css/qtiDefaultRenderer.min.css" media="screen" />
        <?endif;?>
        <?if(get_data('hasMedia')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/css/mediaelementplayer.min.css" media="screen" />
        <?endif;?>
        <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_taobase_www')?>css/custom-theme/jquery-ui-1.8.22.custom.css" />

        <!-- user CSS -->
        <?foreach(get_data('stylesheets') as $stylesheet):?>
            <link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
        <?endforeach?>

        <!-- LIB -->
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery-ui-1.8.23.custom.min.js"></script>
        <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/json.min.js"></script>
        <?if(DEBUG_MODE):?>
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/util.js"></script>
        <?endif;?>

        <?if(get_data('hasMath')):?>
            <!--math rendering-->
            <script type="text/javascript" src="<?=get_data('ctx_math_lib_www')?>MathJax.js?config=TeX-AMS-MML_HTMLorMML-full"></script>
        <?endif;?>
        <?if(get_data('hasGraphics')):?>
            <!--raphael graphic-->
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael.min.js"></script>
            <?if(DEBUG_MODE):?>
                <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael-collision/raphael-collision.js"></script>
            <?else:?>
                <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/raphael/raphael-collision.min.js"></script>
            <?endif;?>
        <?endif?>

        <?if(get_data('hasUpload')):?>
            <link rel="stylesheet" type="text/css" href="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/uploadify.css" media="screen" />
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/jquery.uploadify.v2.1.4.min.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/jquery.uploadify/swfobject.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_taobase_www')?>js/AsyncFileUpload.js"></script>
        <?endif?>

        <!-- JS REQUIRED -->
        <?if(DEBUG_MODE):?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>lib/class.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.ItemLoader.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Qti.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Element.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Object.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Math.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Container.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/feedbacks/class.Feedback.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/feedbacks/class.FeedbackInline.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/feedbacks/class.FeedbackBlock.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/feedbacks/class.ModalFeedback.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.Choice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.ContainerChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.SimpleChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.SimpleAssociableChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.Hottext.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.Hotspot.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.HotspotChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.AssociableHotspot.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.TextVariableChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.InlineChoice.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.GapText.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.GapImg.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/choices/class.Gap.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.Interaction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.InlineInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.EndAttemptInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.InlineChoiceInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.TextEntryInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.Prompt.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.BlockInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.AssociateInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.ChoiceInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.ExtendedTextInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.MatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.OrderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.SliderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.UploadInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.ContainerInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.GapMatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.HottextInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.ObjectInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.MediaInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.GraphicInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.GraphicAssociateInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.GraphicGapMatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.GraphicOrderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.HotspotInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/interactions/class.SelectPointInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.Item.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.ResponseDeclaration.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>src/class.OutcomeDeclaration.js"></script>
        <?else:?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiItem_lib_www')?>qtiItem.min.js"></script>
        <?endif;?>
        <?if(DEBUG_MODE):?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>lib/mustache/mustache.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>src/class.Renderer.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>src/QtiRunner.js"></script>
        <?else:?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>qtiRunner.min.js"></script>
        <?endif;?>
        <?if(get_data('hasMedia')):?>
            <!--Media Element Js lib for audio and video file rendering-->
            <script id="qti_script_mediaelement" type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/mediaelement-and-player.min.js"></script>
        <?endif;?>
        <?if(DEBUG_MODE):?>    
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/class.DefaultRenderer.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/ResultCollector.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.Widget.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.Object.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.StringInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.GraphicInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.EndAttemptInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.InlineChoiceInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.TextEntryInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.AssociateInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.ChoiceInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.ExtendedTextInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.MatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.OrderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.SliderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.UploadInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.GapMatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.HottextInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.MediaInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.GraphicAssociateInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.GraphicGapMatchInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.GraphicOrderInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.HotspotInteraction.js"></script>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>src/widgets/class.SelectPointInteraction.js"></script>
        <?else:?>
            <script type="text/javascript" src="<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>qtiDefaultRenderer.min.js"></script>
        <?endif;?>
        <?if(get_data('clientMatching') && !is_null(get_data('matchingData'))):?>
            <script type="text/javascript" src="<?=get_data('ctx_qti_matching_www')?>QtiRpEngine.min.js"></script>
            <script type="text/javascript">
                var rpEngine = new QtiRpEngine(<?=json_encode(get_data('matchingData'))?>);
                qtiRunner.setResponseProcessing(rpEngine);
            </script>
        <?endif?>

        <!--start the runner-->
        <script type="text/javascript" src="<?=get_data('ctx_qtiRunner_lib_www')?>src/initTaoApis.js"></script>
        <script type="text/javascript">
            var qti_base_www = '';
            var root_url = "<?=get_data('ctx_root_url')?>";
<?if(!is_null(get_data('ctx_debug')) && get_data('ctx_debug')):?>var qti_debug = true;<?endif?>
            var qti_plugin_path = '';
            if($('#qti_script_mediaelement').length){
                qti_plugin_path = $('#qti_script_mediaelement').attr('src').replace('mediaelement-and-player.min.js', '');//dynamically get the plugin path
            }
            var itemData = <?=tao_helpers_Javascript::buildObject(get_data('itemData'))?>;
        </script>
    </head>
    <body>
        <div id="qti_item"></div>
        <div class="qti_control">
            <?if(get_data('ctx_raw_preview')):?>
                <a href="#" id="qti_validate" style="visibility:hidden;"><?=__("Submit");?></a>
            <?else:?>
                <a href="#" id="qti_validate"><?=__("Submit");?></a>
            <?endif?>
        </div>
        <div id="modalFeedbacks"></div>
    </body>
</html>