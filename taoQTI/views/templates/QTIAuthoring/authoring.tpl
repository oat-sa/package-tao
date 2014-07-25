<script type="text/javascript" src="<?=ROOT_URL?>/tao/views/js/json.min.js"></script>
<script type="text/javascript" src="<?=ROOT_URL?>/tao/views/js/jquery.autogrow.js"></script>

<!--qti authoring lib-->
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>authoringConfig.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>shim.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>util.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>QTIauthoringException.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>tinyCarousel.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>IdentifierList.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiEditClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>interactionClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>MathEditor.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>FeedbackEditor.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>class.HtmlEditor.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>class.HtmlEditorItem.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/raphael.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>qtiShapeEditClass.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.extended.js"></script>
<script type="text/javascript" src="<?=get_data('qtiAuthoring_path')?>lib/simplemodal/jquery.simplemodal.js"></script>

<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/jwysiwyg/jquery.wysiwyg.modal.css" type="text/css" />
<link rel="stylesheet" href="<?=get_data('qtiAuthoring_path')?>lib/simplemodal/jquery.simplemodal.css" type="text/css" />
<link rel="stylesheet" href="<?=BASE_WWW?>css/qtiAuthoring.css" type="text/css" />

<!--libs required for dynamic preview-->
<link rel="stylesheet" type="text/css" href="<?=BASE_WWW?>js/qtiDefaultRenderer/lib/mediaelement/css/mediaelementplayer.min.css" media="screen" />
<script type="text/javascript" src="<?=BASE_WWW?>js/qtiDefaultRenderer/lib/mediaelement/mediaelement-and-player.min.js"></script>
<script type="text/javascript" src="<?=BASE_WWW?>js/qtiItem/qtiItem.min.js"></script>
<script type="text/javascript" src="<?=BASE_WWW?>js/qtiRunner/lib/mustache/mustache.js"></script>
<script type="text/javascript" src="<?=BASE_WWW?>js/qtiRunner/src/class.Renderer.js"></script>
<script type="text/javascript" src="<?=BASE_WWW?>js/qtiDefaultRenderer/qtiDefaultRenderer.min.js"></script>
<script type="text/javascript">
    (function(){
        var mathJaxScript = document.createElement("script");
        mathJaxScript.type = "text/javascript";
        mathJaxScript.src = "<?=BASE_WWW?>js/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML-full";
        mathJaxScript[window.opera ? "innerHTML" : "text"] = "MathJax.Hub.Config({showMathMenu:false})";
//        mathJaxScript.src = "http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML";//use cdn
        document.getElementsByTagName("head")[0].appendChild(mathJaxScript);
    })();
    var qti_plugin_path = "<?=get_data('ctx_qtiDefaultRenderer_lib_www')?>lib/mediaelement/";
</script>
<div id="qtiAuthoring_loading">
    <div id="qtiAuthoring_loading_message">
        <img src="<?=ROOT_URL?>/tao/views/img/ajax-loader.gif" alt="loading" />
    </div>
</div>

<div id="qtiAuthoring_main_container">
    <div id="qtiAuthoring_title_container" class="ui-widget-content ui-corner-top" style="display:none;">
    </div>

    <div id="qtiAuthoring_menu_container" class="ui-widget-content ui-corner-top">
        <div id="qtiAuthoring_menu_left_container" class="ui-widget-header">
            <div id="qtiAuthoring_save_button" class="qti-menu-item">
                <img title="<?=__('Save')?>" src="<?=get_data('qtiAuthoring_img_path')?>document-save.png"/>
                <br/>
                <a href="#"><?=__('Save')?></a>
            </div>

            <div id="qtiAuthoring_preview_button" class="qti-menu-item">
                <img title="<?=__('Popup Preview')?>" src="<?=get_data('qtiAuthoring_img_path')?>view-fullscreen.png"/>
                <br/>
                <a href="#"><?=__('Popup Preview')?></a>
            </div>
<?if(DEBUG_MODE && true):?>
                <div id="qtiAuthoring_export_button" class="qti-menu-item">
                    <img title="<?=__('Export')?>" src="<?=ROOT_URL?>/tao/views/img/actions/export.png"/>
                    <br/>
                    <a href="#"><?=__('Export')?></a>
                </div>

                <div id="qtiAuthoring_debug_button" class="qti-menu-item">
                    <img title="<?=__('Debug')?>" src="<?=get_data('qtiAuthoring_img_path')?>bug.png"/>
                    <br/>
                    <a href="#"><?=__('Debug')?></a>
                </div>
<?endif;?>
        </div>
        <div id="qtiAuthoring_menu_right_container">
            <div id="qtiAuthoring_item_editor_button" class="qti-menu-item qti-menu-item-wide">
                <img title="<?=__('Return to item editor')?>" src="<?=get_data('qtiAuthoring_img_path')?>return.png"/>
                <br/>
                <a href="#"><?=__('Return to item editor')?></a>
            </div>

            <div id="qtiAuthoring_menu_interactions">
                <div id ="qti-carousel-prev" class="qti-carousel-button">
                    <img id="qti-carousel-prev-button" title="<?=__('Prev')?>" src="<?=get_data('qtiAuthoring_img_path')?>go-previous-view.png"/>
                </div>
                <div id ="qti-carousel-container">
                    <div id ="qti-carousel-content"></div>
                </div>
                <div id ="qti-carousel-next" class="qti-carousel-button">
                    <img id="qti-carousel-next-button" title="<?=__('Next')?>" src="<?=get_data('qtiAuthoring_img_path')?>go-next-view.png"/>
                </div>
            </div>

            <div id="qtiAuthoring_menu_interactions_overlay" class="ui-widget-overlay"></div>
        </div>
    </div>
</div>
<div id="tabs-qti">

    <ul id="tabs-qti-menu">
        <li><a href="#qtiAuthoring_item_container"></a></li>
        <li><a href="#qtiAuthoring_interaction_container"></a></li>
    </ul>

    <div id="qtiAuthoring_item_container">
        <div id="qtiAuthoring_item_left_container">
            <div id="item_option_accordion">
                <h3><a href="#"><?=__('QTI Item Attributes')?></a></h3>
                <div id="qtiAuthoring_itemProperties" class="ui-widget-content ui-corner-bottom">
<?=get_data('itemForm')?>
                </div>
                <h3><a href="#"><?=__('Response processing template editor')?></a></h3>
                <div id="qtiAuthoring_processingEditor" class="ui-widget-content ui-corner-bottom"></div>
                <h3><a href="#"><?=__('Stylesheets manager')?></a></h3>
                <div id="qtiAuthoring_cssManager" class="ui-widget-content ui-corner-bottom main-container"></div>
            </div>
        </div>

        <div id="qtiAuthoring_item_right_container">
            <div id="qtiAuthoring_itemEditor" class="ui-widget-content ui-corner-bottom">
                <textarea name="wysiwyg" id="itemEditor_wysiwyg"><?=_dh(get_data('itemData'))?></textarea>
            </div>
        </div>

        <div style="clear:both"></div>
    </div>

    <div id="qtiAuthoring_interaction_container">
    </div>

</div>



<div id="dialog-confirm" title="" style="display:none;"><span class="ui-icon ui-icon-alert"></span><p id="dialog-confirm-message"></p></div>

<script type="text/javascript">
    $(document).ready(function(){

        if($.browser.chrome || $.browser.webkit){
            $('#qtiAuthoring_itemProperties').height('483.6px');
            $('#qtiAuthoring_processingEditor').height('483.6px');
            $('#qtiAuthoring_cssManager').height('483.6px');
        }

        //init interface:
        $myTab = $("#tabs-qti");
        if($myTab.tabs){
            $myTab.tabs({
                select : function(event, ui){
                    if(ui.index == 0 || ui.index == 1){
                        return true;
                    }
                    return false;
                }
            });
        }


        $('#tabs-qti-menu').hide();
        $('#qtiAuthoring_item_editor_button').hide();

        //init item editor:
        try{

            //global item object
            qtiEdit.setFrameCSS([
                "<?=BASE_WWW?>css/normalize.css",
                "<?=BASE_WWW?>css/base.css",
                "<?=BASE_WWW?>css/qtiAuthoringFrame.css"
            ]);
            qtiEdit.itemSerial = '<?=get_data('itemSerial')?>';
            myItem = new qtiEdit('<?=get_data('itemSerial')?>');
            
            qtiEdit.idList = new IdentifierList(<?=get_data('identifierList')?>);

            //set item name in title bar (disabled)
            var titleInput = $('#AssessmentItem_Form').find('input#title');
            if(titleInput.length){
                qtiEdit.setTitleBar($(titleInput[0]).val());
            }

            //prevent item form submission from other method than ajax
            $('#AssessmentItem_Form').submit(function(){
                return false;
            });
        }catch(err){
            CL('error creating the item', err);
        }

        //link the qti object to the item rdf resource
        myItem.itemUri = '<?=get_data('itemUri')?>';
        myItem.itemClassUri = '<?=get_data('itemClassUri')?>';

        //set the save button:
        $('#qtiAuthoring_save_button').click(function(){
            myItem.save();
            return false;
        });

        //set the preview button:
        $('#qtiAuthoring_preview_button').click(function(){
            myItem.preview();
            return false;
        });

<?if(DEBUG_MODE):?>
            //set debug button
            $('#qtiAuthoring_debug_button').click(function(){
                myItem.debug();
                return false;
            });

            $('#qtiAuthoring_export_button').click(function(){
                myItem.exportItem();
                return false;
            });
<?endif;?>
        require(['jqueryUI'], function(){
            $("#item_option_accordion").accordion({
                heightStyle : "fill",
                fillSpace : true
            });
        });



        myItem.loadStyleSheetForm();

        var interactionTypes = qtiEdit.availableInteractions();
        for(var interactionType in interactionTypes){
            var id = 'add_' + interactionType + '_interaction';
            var $menuItem = $('<div/>');
            $menuItem.attr('id', id);
            $menuItem.addClass('qti-menu-item');
            $menuItem.appendTo($('#qti-carousel-content'));

            var label = interactionTypes[interactionType]['short'];
            var $imgElt = $('<img/>');
            $imgElt.attr('title', label);
            $imgElt.attr('src', interactionTypes[interactionType].icon);
            $menuItem.append($imgElt);
            $menuItem.append('<br/>');
            $menuItem.append('<a href="#">' + label + '</a>');
            $imgElt.on('drag dragstart', function(e){
                e.preventDefault();
            });
            $('#qtiAuthoring_itemEditor').find('li.' + id).hide();
            $menuItem.bind('click', {id : id}, function(e){
                $('#qtiAuthoring_itemEditor').find('li.' + e.data.id).click();
            });
        }

        setTimeout(function(){

            $('#qtiAuthoring_loading').hide();
            $('#qtiAuthoring_main_container').show();
            var qtiInteractionCarousel = new tinyCarousel('#qti-carousel-container', '#qti-carousel-content', '#qti-carousel-next-button', '#qti-carousel-prev-button');

            //init interactions button carousel:
            $(window).unbind('resize').resize(function(){
                qtiInteractionCarousel.update();
            });

        }, 1000);

    });
</script>
