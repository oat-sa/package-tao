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

require_once dirname(__FILE__).'/../../generis/common/inc.extension.php';
include_once dirname(__FILE__).'/../includes/raw_start.php';
define('PATH_SAMPLE', dirname(__FILE__).'/samples/');
define('URL_SAMPLE', BASE_URL.'test/samples/test_base_www/');
?><!DOCTYPE html>
<html>
    <head>
        <title>QUnit QTI Model Test Suite</title>
        <link rel="stylesheet" href="../../tao/test/qunit/qunit.css" type="text/css" media="screen">
        <link rel="stylesheet" type="text/css" href="../../tao/views/css/custom-theme/jquery-ui-1.8.22.custom.css" />
        <!--<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->

        <script type="text/javascript" src="../../tao/views/js/jquery-1.8.0.min.js"></script>
        <script type="text/javascript" src="../../tao/views/js/jquery-ui-1.8.23.custom.min.js"></script>
        <script type="text/javascript" src="../../tao/views/js/json.min.js"></script>
        <script type="text/javascript" src="../../tao/views/js/raphael/raphael.min.js"></script>
        <script type="text/javascript" src="../../tao/views/js/raphael/raphael-collision/raphael-collision.js"></script>

        <script type="text/javascript" src="../../tao/test/qunit/qunit.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/lib/class.js"></script>

        <script type="text/javascript" src='../../tao/views/js/util.js'></script>
        
        <!--math jax lib-->
        <script type="text/javascript" src="../views/js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full"></script>

        <!--qti model lib-->
        <script type="text/javascript" src="../views/js/qtiItem/src/class.ItemLoader.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Qti.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Element.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Object.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Math.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Container.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/feedbacks/class.Feedback.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/feedbacks/class.FeedbackInline.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/feedbacks/class.FeedbackBlock.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/feedbacks/class.ModalFeedback.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.Choice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.ContainerChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.SimpleChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.SimpleAssociableChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.Hottext.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.Hotspot.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.HotspotChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.AssociableHotspot.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.TextVariableChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.InlineChoice.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.GapText.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.GapImg.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/choices/class.Gap.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.Interaction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.InlineInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.EndAttemptInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.InlineChoiceInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.TextEntryInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.Prompt.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.BlockInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.AssociateInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.ChoiceInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.ExtendedTextInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.MatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.OrderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.SliderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.UploadInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.ContainerInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.GapMatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.HottextInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.ObjectInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.MediaInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.GraphicInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.GraphicAssociateInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.GraphicGapMatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.GraphicOrderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.HotspotInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/interactions/class.SelectPointInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.Item.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.ResponseDeclaration.js"></script>
        <script type="text/javascript" src="../views/js/qtiItem/src/class.OutcomeDeclaration.js"></script>

        <!--runtime lib-->
        <script type="text/javascript" src="../views/js/qtiRunner/lib/mustache/mustache.js"></script>
        <script type="text/javascript" src="../views/js/qtiRunner/src/class.Renderer.js"></script>

        <!--legacy renderer-->

        <link rel="stylesheet" type="text/css" href="../views/js/qtiDefaultRenderer/lib/mediaelement/css/mediaelementplayer.min.css" media="screen" />
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/lib/mediaelement/mediaelement-and-player.min.js"></script>

        <link rel="stylesheet" type="text/css" href="../views/js/qtiDefaultRenderer/css/qti.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../views/css/normalize.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="../views/css/base.css" media="screen" />
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/class.DefaultRenderer.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/ResultCollector.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.Widget.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.Object.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.StringInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.GraphicInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.EndAttemptInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.InlineChoiceInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.TextEntryInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.AssociateInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.ChoiceInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.ExtendedTextInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.MatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.OrderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.SliderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.UploadInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.GapMatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.HottextInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.MediaInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.GraphicAssociateInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.GraphicGapMatchInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.GraphicOrderInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.HotspotInteraction.js"></script>
        <script type="text/javascript" src="../views/js/qtiDefaultRenderer/src/widgets/class.SelectPointInteraction.js"></script>

        <!--Test Case-->
        <script type="text/javascript" src="js/testers/class.Tester.Interaction.js"></script>
        <script>
            //setting globals need to render img correctly
            var qti_plugin_path = '<?=BASE_WWW.'js/qtiDefaultRenderer/lib/mediaelement/'?>';
            var qti_base_www = '<?=URL_SAMPLE?>';
            var qti_debug = true;

            //load all item samples
            var allItems = <?=file_get_contents(PATH_SAMPLE.'json/ALL.json')?>;
        </script>
        <script type="text/javascript" src="js/QTIModelTestCase.js"></script>

    </head>
    <body>
        <h1 id="qunit-header">QUnit QTI Model Test Suite</h1>
        <h2 id="qunit-banner"></h2>
        <div id="qunit-testrunner-toolbar"></div>
        <h2 id="qunit-userAgent"></h2>
        <ol id="qunit-tests"></ol>
        <div id="qunit-fixture">test markup</div>
        <div id="tmp"></div>
    </body>
</html>