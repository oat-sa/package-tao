<?php
/**
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

/**
 * QtiAuthoring Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoQTI_actions_QtiAuthoring extends tao_actions_CommonModule
{

    protected $debugMode = false;

    /**
     * @var taoQTI_models_classes_QTI_Service
     */
    private $qtiService;
    private $currentElement = null;
    private $item = null;
    private $interaction = null;
    private $choice = null;
    private $object = null;
    private $response = null;

    /**
     * constructor: initialize the service and the default data
     * @access public
     */
    public function __construct(){

        parent::__construct();

        $this->debugMode = false;
        $this->qtiService = taoQTI_models_classes_QTI_Service::singleton();
        $this->service = taoQTI_models_classes_QtiAuthoringService::singleton();
        $this->defaultData();
        $this->item = $this->getCurrentItem();
    }

    /**
     * Returns the current rdf item
     * 
     * @access public
     * @return core_kernel_classes_Resource
     */
    public function getCurrentItemResource(){

        $itemResource = null;

        if($this->hasRequestParameter('itemUri')){
            $itemResource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('itemUri')));
        }else{
            throw new common_Exception('no item rsource uri found');
        }

        return $itemResource;
    }

    /**
     * Get the current QTI item object
     * Load the QTI object either from the file or from session or create a new one
     * 
     * @access public
     * @return taoQTI_models_classes_QTI_Item 
     */
    public function getCurrentItem(){

        if(is_null($this->item)){

            if($this->hasRequestParameter('itemSerial')){

                $itemSerial = tao_helpers_Uri::decode($this->getRequestParameter('itemSerial'));
                $this->item = $this->qtiService->getItemFromSession($itemSerial);
            }elseif($this->hasRequestParameter('instance')){

                $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
                $itemResource = new core_kernel_classes_Resource($itemUri);
                $this->item = $this->qtiService->getDataItemByRdfItem($itemResource);

                if(is_null($this->item)){
                    //create a new item object:
                    $itemIdentifier = tao_helpers_Uri::getUniqueId($itemUri); //TODO: remove coopling to TAO
                    $this->item = $this->service->createNewItem($itemIdentifier, $itemResource->getLabel());
                }
            }

            if(is_null($this->item)){
                throw new common_Exception('there is no current item defined');
            }
        }

        return $this->item;
    }

    /**
     * Load the main view for the authoring tool
     * 
     * @access public
     */
    public function index(){

        //clear the QTI session data before doing anything else:
        taoQTI_models_classes_QTI_QTISessionCache::singleton()->purge();

        //required for saving the item in tao:
        $itemUri = $this->getRequestParameter('instance');
        $this->setData('itemUri', tao_helpers_Uri::encode($itemUri));

        $itemResource = new core_kernel_classes_Resource($itemUri);
        foreach($itemResource->getTypes() as $itemClass){
            $this->setData('itemClassUri', tao_helpers_Uri::encode((!is_null($itemClass)) ? $itemClass->getUri() : ''));
            break;
        }

        $currentItem = $this->getCurrentItem();
        $itemData = $this->service->getItemData($currentItem); //issue here?
        
        //prepare identifier list:
        $identifiedEls = $currentItem->getIdentifiedElements()->get();
        $identifierList = array();

        foreach($identifiedEls as $id => $serials){
            foreach($serials as $serial => $elt){
                $identifierList[$serial] = $id;
            }
        }

        $this->setData('itemSerial', $currentItem->getSerial());
        $this->setData('itemForm', $currentItem->toForm()->render());
        $this->setData('itemData', $itemData);
        $this->setData('identifierList', tao_helpers_Javascript::buildObject($identifierList));
        $this->setData('jsFramework_path', BASE_WWW.'js/jsframework/');
        $this->setData('qtiAuthoring_path', BASE_WWW.'js/qtiAuthoring/');
        $this->setData('qtiAuthoring_img_path', BASE_WWW.'img/qtiAuthoring/');
        $this->setData('ctx_qtiDefaultRenderer_lib_www', BASE_WWW.'js/qtiDefaultRenderer/');

        if(isset($_GET['STANDALONE_MODE']) && $_GET['STANDALONE_MODE']){
            $this->setData('includedView', DIR_VIEWS.'templates/'."QTIAuthoring/authoring.tpl");
            return parent::setView('sas.tpl', true);
        }else{
            $this->setView("QTIAuthoring/authoring.tpl");
        }
    }

    /**
     * Load the main view for the authoring tool
     * Ajax call only.
     * 
     * @access public
     */
    public function saveItemData(){
        $saved = false;

        $itemData = $this->getPostedItemData();

        if(!empty($itemData)){
            //save to qti:
            $this->service->saveItemData($this->getCurrentItem(), $itemData);
            $saved = true;
        }

        echo json_encode(array(
            'saved' => $saved
        ));
    }

    /**
     * Save the item data and properties to session, then save the complete object to the item's QTI XML file
     * Ajax call only.
     * 
     * @access public
     * @return boolean
     */
    public function saveItem(){
        $saved = false;

        $itemData = $this->getPostedItemData();

        $itemObject = $this->getCurrentItem();
        //save item properties in the option array:
        $options = array(
            'title' => $itemObject->getIdentifier(),
            'label' => '',
            'timeDependent' => false,
            'adaptive' => false
        );
        if($this->getRequestParameter('title') != ''){
            $options['title'] = $this->getRequestParameter('title');
        }
        if($this->hasRequestParameter('label')){
            $options['label'] = $this->getRequestParameter('label');
        }
        if($this->hasRequestParameter('timeDependent')){
            $options['timeDependent'] = $this->getRequestParameter('timeDependent');
        }
        if($this->hasRequestParameter('adaptive')){
            $options['adaptive'] = $this->getRequestParameter('adaptive');
        }

        $this->service->setOptions($itemObject, $options);

        if(!empty($itemData)){
            $this->service->saveItemData($itemObject, $itemData);
        }

        $itemResource = $this->getCurrentItemResource();
        $saved = $this->qtiService->saveDataItemToRdfItem($itemObject, $itemResource);

        if(tao_helpers_Request::isAjax()){
            echo json_encode(array(
                'saved' => $saved
            ));
        }

        return $saved;
    }

    /**
     * Dump the entire taoQTI_models_classes_QTI_Item from the session for debugging purpose.
     * Not suitable for production use.
     * 
     * @access public
     */
    public function debug(){
        $itemObject = $this->getCurrentItem();
        $this->setData('itemObject', $itemObject->toArray());
        $this->setData('sessionData', array('not supported'));
        $this->setView("QTIAuthoring/debug.tpl");
    }

    /**
     * Returns cleaned posted QTI item data/
     * 
     * @access protected
     * @return string
     */
    protected function getPostedItemData(){

        $returnValue = $_POST['itemData'];
        $returnValue = $this->service->restorePlaceholders($this->getCurrentItem(), $returnValue);
        $returnValue = $this->cleanPostedData($returnValue);

        return $returnValue;
    }

    /**
     * Returns cleaned posted QTI interaction data
     * 
     * @access protected
     * @return string
     */
    protected function getPostedInteractionData(){

        $interactionBody = isset($_POST['interactionData']) ? $_POST['interactionData'] : '';
        $interactionBody = $this->service->restorePlaceholders($this->getCurrentInteraction(), $interactionBody);
        
        //wrap with a block tag to force blockWrapping in purifier
        $interactionBody = '<blockquote>'.$interactionBody.'</blockquote>';
        $interactionBody = $this->cleanPostedData($interactionBody);
        $interactionBody = substr(trim($interactionBody), 12, -13);
        
        return $interactionBody;
    }

    /**
     * Returns cleaned posted data
     * 
     * @param string
     * @param boolean
     * @access protected
     * @return string
     */
    protected function getPostedData($key, $required = false, $filterModel = 'blockStatic'){

        $returnValue = '';

        if($this->hasRequestParameter($key)){
            $returnValue = $this->cleanPostedData($_POST[$key], $filterModel);
        }else{
            if($required){
                throw new common_Exception('the request data "'.$key.'" cannot be found');
            }
        }

        return $returnValue;
    }

    /**
     * Clean data with a selectable filter model
     * 
     * @access protected
     * @param string
     * @param string
     * @return string
     */
    protected function cleanPostedData($data, $filterModel = 'blockStatic'){

        $data = taoQTI_helpers_qti_ItemAuthoring::restoreMediaResourceUrl($data);
        $returnValue = taoQTI_helpers_qti_ItemAuthoring::cleanHTML($data, $filterModel);
        
        return $returnValue;
    }

    /**
     * Add a QTI interaction to the current QTI item
     * 
     * @access public
     */
    public function addInteraction(){

        $added = false;
        $interactionSerial = '';
        $interactionType = $this->getRequestParameter('interactionType');
        $itemData = $this->getPostedItemData();
        $item = $this->getCurrentItem();

        if(!empty($interactionType)){
            $interaction = $this->service->addInteraction($item, $interactionType, $itemData);
            if(!is_null($interaction)){
                $added = true;
                $interactionSerial = $interaction->getSerial();
                $itemData = $this->service->getItemData($item);
            }
        }

        echo json_encode(array(
            'added' => $added,
            'interactionSerial' => $interactionSerial,
            'itemData' => $itemData
        ));
    }

    /**
     * works for gap-style choices (gap+hottext)
     */
    public function addGap(){

        $added = false;
        $choiceSerial = ''; //the hot text basically is a "choice"
        $choiceForm = '';

        $interactionData = $this->getPostedInteractionData();
        $interaction = $this->getCurrentInteraction();
        if($interaction instanceof taoQTI_models_classes_QTI_container_FlowContainer){
            $choice = $this->service->createGap($interaction, $interactionData);
        }else{
            throw new common_Exception('wrong interaction type to add a gap to');
        }

        if(!is_null($choice)){
            $interactionData = $this->service->getInteractionData($interaction); //do not convert to html entities...
            //everything ok:
            $added = true;
            $choiceSerial = $choice->getSerial();
            $choiceForm = $choice->toForm()->render();
        }

        echo json_encode(array(
            'added' => $added,
            'choiceSerial' => $choiceSerial,
            'choiceForm' => $choiceForm,
            'interactionData' => $interactionData
        ));
    }

    /**
     * Add a choice to the current QTI interaction
     * 
     * @access public
     */
    public function addChoice(){
        $added = false;
        $choiceSerial = '';
        $choiceForm = '';

        $interaction = $this->getCurrentInteraction();
        if(!is_null($interaction)){
            $setId = null;
            if($this->hasRequestParameter('setId')){//gap match interaction
                $setId = $this->getRequestParameter('setId');
            }

            $choice = $this->service->createChoice($interaction, $setId);


            $choiceSerial = $choice->getSerial();
            $choiceForm = $choice->toForm()->render();

            $added = true;
        }

        echo json_encode(array(
            'added' => $added,
            'choiceSerial' => $choiceSerial,
            'choiceForm' => $choiceForm
        ));
    }

    /**
     * Delete the current choice from the current QTI interaction
     * 
     * @access public
     */
    public function deleteChoice(){

        $interaction = $this->getCurrentInteraction();
        $choice = $this->getCurrentChoice();
        $deleted = false;

        if(!is_null($interaction) && !is_null($choice)){
            $deleted = $this->service->deleteChoice($interaction, $choice);
        }

        echo json_encode(array(
            'deleted' => $deleted,
            'reload' => false, //@see deprecated requireChoicesUpdate()
            'reloadInteraction' => ($deleted) ? $this->requireInteractionUpdate($interaction) : false
        ));
    }

    /**
     * Tells if all choices need to be updated if one of them has been updated
     * (deprecated : matchgroup is deprecated in QTI v 2.1)
     * 
     * @access protected
     */
    protected function requireChoicesUpdate(taoQTI_models_classes_QTI_interaction_Interaction $interaction){

        $reload = false;

        //basically, interactions that have choices with the "matchgroup" property
        if(!is_null($interaction)){
            switch(strtolower($interaction->getType())){
                case 'associate':
                case 'match':
                case 'gapmatch':
                case 'graphicgapmatch':{
                        $reload = true;
                        break;
                    }
            }
        }

        return $reload;
    }

    /**
     * Tells if an interaction needs to be updated if a choice has been updated
     * 
     * @access protected
     */
    protected function requireInteractionUpdate(taoQTI_models_classes_QTI_interaction_Interaction $interaction){

        $reload = false;

        //basically, interactions that need a wysiwyg data editor:
        if($this->getRequestParameter('reloadInteraction')){
            if(!is_null($interaction)){
                switch(strtolower($interaction->getType())){
                    case 'hottext':
                    case 'gapmatch':{
                            $reload = true;
                            break;
                        }
                }
            }
        }

        return $reload;
    }

    /**
     * Gives the editing tag for an interaction.
     * (method to be called to dynamically update the main itemData editor frame)
     * 
     * @access public
     */
    public function getInteractionTag(){
        $interaction = $this->getCurrentInteraction();
        echo $this->service->getInteractionTag($interaction);
    }

    /**
     * Get the current QTI interaction we are working on
     * 
     * @access public
     * @return taoQTI_models_classes_QTI_interaction_Interaction
     */
    public function getCurrentInteraction(){

        if(is_null($this->interaction)){
            if($this->hasRequestParameter('interactionSerial')){
                $serial = $this->getRequestParameter('interactionSerial');
                $interactions = $this->getCurrentItem()->getInteractions();
                if(isset($interactions[$serial])){
                    $this->interaction = $interactions[$serial];
                }else{
                    throw new common_Exception('cannot find the interaction from current item');
                }
            }else{
                throw new common_Exception('no request parameter "interactionSerial" found');
            }
        }

        return $this->interaction;
    }

    /**
     * Get the current QTI choice we are working on
     * 
     * @access public
     * @return taoQTI_models_classes_QTI_choice_Choice
     */
    public function getCurrentChoice(){

        if(is_null($this->choice)){
            if($this->hasRequestParameter('choiceSerial')){
                $serial = $this->getRequestParameter('choiceSerial');
                $choice = $this->getCurrentInteraction()->getChoiceBySerial($serial);
                if(!is_null($choice)){
                    $this->choice = $choice;
                }else{
                    throw new common_Exception('cannot find the choice from current interaction');
                }
            }else{
                throw new common_Exception('no request parameter "choiceSerial" found');
            }
        }

        return $this->choice;
    }

    /**
     * Get the current QTI response
     * 
     * @access public
     * @return taoQTI_models_classes_QTI_Response
     */
    public function getCurrentResponse(){

        if(is_null($this->response)){
            if($this->hasRequestParameter('responseSerial')){
                $serial = $this->getRequestParameter('responseSerial');
                $responses = $this->getCurrentItem()->getResponses();
                if(isset($responses[$serial])){
                    $this->response = $responses[$serial];
                }else{
                    throw new common_exception_Error('cannot find the response from the current item');
                }
            }elseif($this->hasRequestParameter('interactionSerial')){
                $this->response = $this->getCurrentInteraction()->getResponse();
            }else{
                throw new common_Exception('missing request parameter to find the current response (responseSerial or interactionSerial)');
            }
        }

        return $this->response;
    }

    /**
     * Get the current response processing
     * 
     * @access public
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public function getCurrentResponseProcessing(){
        return $this->service->getResponseProcessing($this->getCurrentItem());
    }

    /**
     * Get the current outcome to be edited
     * 
     * @return taoQTI_models_classes_QTI_Outcome 
     */
    public function getCurrentOutcome(){
        $returnValue = null;
        if($this->hasRequestParameter('outcomeSerial')){
            $serial = $this->getRequestParameter('outcomeSerial');
            $outcome = $this->getCurrentItem()->getOutcome($serial);
            if(!empty($outcome)){
                $returnValue = $outcome;
            }else{
                throw new common_Exception('cannotthe outcome serial in the item');
            }
        }else{
            throw new common_Exception('cannot find the outcome request parameter "outcomeSerial"');
        }

        return $returnValue;
    }

    /**
     * Return the interaction editing interface.
     * (To be called at the same time as edit response)
     * 
     * @access public
     */
    public function editInteraction(){

        $interaction = $this->getCurrentInteraction();

        //build the form with its method "toForm"
        $myForm = $interaction->toForm();

        //get the itnteraction's choices

        $choiceForms = array();

        $interactionType = strtolower($interaction->getType());
        switch($interactionType){
            case 'match':{
                    $groupSerials = array();
                    for($set = 0; $set < 2; $set++){
                        $groupSerials[$set] = $set;
                        $group = $interaction->getChoices($set);
                        $choiceForms[$set] = array();
                        foreach($group as $choice){
                            $choiceForms[$set][$choice->getSerial()] = $choice->toForm()->render();
                        }
                    }
                    $this->setData('groupSerials', $groupSerials);
                    break;
                }
            case 'gapmatch':{
                    /*
                      //get group form:
                      $groupForms = array();
                      foreach($this->service->getInteractionGroups($interaction) as $group){
                      //order does not matter:
                      $groupForms[] = $group->toForm($interaction)->render();
                      }
                      $this->setData('formGroups', $groupForms);

                      //get choice forms:
                      foreach($choices as $order=>$choice){
                      $choiceForms[$choice->getSerial()] = $choice->toForm()->render();
                      }
                     */
                    break;
                }
            //graphic interactions:
            case 'graphicgapmatch':{
                    $groups = array();
                    foreach($interaction->getGapImgs() as $gap){
                        $groups[] = $gap->getSerial();
                    }
                    $this->setData('groups', $groups);
                }
            case 'hotspot':
            case 'graphicorder':
            case 'graphicassociate':
            case 'selectpoint':
            case 'positionobject':{
                    $object = $interaction->getObject();
                    $this->setData('backgroundImagePath', $object->getAttributeValue('data'));
                    $this->setData('width', (intval($object->getAttributeValue('width')) > 0) ? $object->getAttributeValue('width') : '');
                    $this->setData('height', (intval($object->getAttributeValue('height')) > 0) ? $object->getAttributeValue('height') : '');
                    break;
                }
            case 'media':{
                    $object = $interaction->getObject();
                    $this->setData('mediaFilePath', $object->getAttributeValue('data'));
                    $this->setData('mediaFileWidth', (intval($object->getAttributeValue('width')) > 0) ? $object->getAttributeValue('width') : '');
                    $this->setData('mediaFileHeight', (intval($object->getAttributeValue('height')) > 0) ? $object->getAttributeValue('height') : '');
                    $type = trim($object->getAttributeValue('type'));
                    $this->setData('mediaFileType', empty($type) ? '' : $object->getAttributeValue('type'));
                    break;
                }
            default:{
                    foreach($interaction->getChoices() as $choice){
                        $choiceForms[$choice->getSerial()] = $choice->toForm()->render();
                    }
                }
        }

        //add tips:
        switch($interactionType){
            case 'associate':{
                    $this->setData('response_grid_tip', __('The order of the choices does not matter.').' '.__('Both associations “choice 1 / choice 2” and “choice 2 / choice 1” are equivalent.'));
                    break;
                }
        }

        //display the template, according to the type of interaction
        $templateName = 'QTIAuthoring/form_interaction_'.strtolower($interaction->getType()).'.tpl';
        $this->setData('interactionType', ucfirst($interaction->getType()));
        $this->setData('interactionSerial', $interaction->getSerial());
        $this->setData('formInteraction', $myForm->render());
        $this->setData('formChoices', $choiceForms);
        $this->setData('interactionData', $this->service->getInteractionData($interaction));
        $this->setView($templateName);
    }

    /**
     * Display the choices editing view
     * 
     * - called on interaction edit form loaded
     * - called when the choices forms need to be reloaded
     * 
     * @access public
     */
    public function editChoices(){

        $interaction = $this->getCurrentInteraction();

        //get the itnteraction's choices
        $choiceForms = array();

        $interactionType = strtolower($interaction->getType());
        switch($interactionType){
            case 'match':{
                    $groupSerials = array();
                    for($set = 0; $set < 2; $set++){
                        $groupSerials[$set] = $set;
                        $group = $interaction->getChoices($set);
                        $choiceForms[$set] = array();
                        foreach($group as $choice){
                            $choiceForms[$set][$choice->getSerial()] = $choice->toForm()->render();
                        }
                    }
                    $this->setData('groupSerials', $groupSerials);
                    break;
                }
            case 'gapmatch':{
                    $gapForms = array();
                    foreach($interaction->getGaps() as $gap){
                        //order does not matter:
                        $gapForms[$gap->getSerial()] = $gap->toForm($interaction)->render();
                    }
                    $this->setData('formGroups', $gapForms);

                    //get choice forms:
                    foreach($interaction->getChoices() as $order => $choice){
                        $choiceForms[$choice->getSerial()] = $choice->toForm()->render();
                    }
                    break;
                }
            case 'graphicgapmatch':{
                    //get gapImgs form:
                    foreach($interaction->getGapImgs() as $gapImg){
                        //order does not matter:
                        $choiceForms[$gapImg->getSerial()] = $gapImg->toForm()->render();
                    }

                    //get hotspot forms:
                    $hotspotForms = array();
                    foreach($interaction->getChoices() as $order => $hotspot){
                        $hotspotForms[$hotspot->getSerial()] = $hotspot->toForm()->render();
                    }
                    $this->setData('formGroups', $hotspotForms);
                    break;
                }
            default:{
                    //get choice forms:
                    foreach($interaction->getChoices() as $order => $choice){
                        $choiceForms[$choice->getSerial()] = $choice->toForm()->render();
                    }
                }
        }

        $templateName = 'QTIAuthoring/form_choices_'.strtolower($interaction->getType()).'.tpl';
        $this->setData('choiceType', ucfirst($this->service->getInteractionChoiceName($interactionType)).'s');
        $this->setData('formChoices', $choiceForms);
        $this->setView($templateName);
    }

    /**
     * Save the QTI interaction properties from the editing form to session
     * 
     * @access public
     */
    public function saveInteraction(){

        $interaction = $this->getCurrentInteraction();

        $myForm = $interaction->toForm();

        $saved = false;
        $reloadResponse = false;
        $newGraphicObject = array();
        $newMediaObject = array();

        if($myForm->isSubmited()){
            if($myForm->isValid()){
                $values = $myForm->getValues();

                if(isset($values['interactionIdentifier'])){
                    if($values['interactionIdentifier'] != $interaction->getIdentifier()){
                        $this->service->setIdentifier($interaction, $values['interactionIdentifier']);
                    }
                    unset($values['interactionIdentifier']);
                }

                if($interaction instanceof taoQTI_models_classes_QTI_interaction_BlockInteraction){
                    //for block interactions
                    if(isset($values['prompt'])){
                        $this->service->setPrompt($interaction, $this->getPostedData('prompt', true, 'inlineStatic'));
                        unset($values['prompt']);
                    }
                }

                if($interaction instanceof taoQTI_models_classes_QTI_interaction_ObjectInteraction){

                    $object = $interaction->getObject();

                    //for graphic interactions:
                    $objectAttributes = array();
                    if(isset($values['object_width'])){
                        if(intval($values['object_width']) == 0){
                            $object->removeAttributeValue('width'); //authorize empty value for optional attr
                        }else{
                            $objectAttributes['width'] = intval($values['object_width']);
                        }
                        unset($values['object_width']);
                    }

                    if(isset($values['object_height'])){
                        if(intval($values['object_height']) == 0){
                            $object->removeAttributeValue('height'); //authorize empty value for optional attr
                        }else{
                            $objectAttributes['height'] = intval($values['object_height']);
                        }
                        unset($values['object_height']);
                    }

                    if(isset($values['object_data'])){

                        //get mime type
                        $mediaFilePath = trim($values['object_data']);
                        unset($values['object_data']);

                        if($interaction instanceof taoQTI_models_classes_QTI_interaction_GraphicInteraction){
                            $imgProperties = $this->getMediaProperties($mediaFilePath, array('image'));
                            if(!empty($imgProperties)){
                                $objectAttributes['data'] = $mediaFilePath;
                                $objectAttributes['type'] = $imgProperties['mime'];
                                $newGraphicObject = $objectAttributes;
                            }else{
                                $newGraphicObject['errorMessage'] = __('invalid image mime type');
                            }
                        }else if($interaction instanceof taoQTI_models_classes_QTI_interaction_MediaInteraction){
                            $mediaProperties = $this->getMediaProperties($mediaFilePath, array('audio', 'video'));
                            if(!empty($mediaProperties)){
                                $objectAttributes['data'] = $mediaFilePath;
                                $objectAttributes['type'] = $mediaProperties['mime'];
                                $newMediaObject = $objectAttributes;
                            }else{
                                $newMediaObject['errorMessage'] = __('invalid media mime type');
                            }
                        }
                    }

                    $object->setAttributes($objectAttributes);
                }else if($interaction instanceof taoQTI_models_classes_QTI_interaction_ContainerInteraction){
                    $this->service->saveInteractionData($interaction, $this->getPostedInteractionData());
                }

                unset($values['interactionSerial']);

                foreach($values as $key => $value){
                    if(preg_match('/^max/', $key)){
                        if($interaction->getAttributeValue($key) != $value){
                            $reloadResponse = true;
                        }
                        break;
                    }
                }

                //save all options before updating the interaction response
                $this->service->editOptions($interaction, $values);
                if($reloadResponse){
                    //update the cardinality, just in case it has been changed:
                    //may require upload of the response form, since the maximum allowed response may have changed!
                    $this->service->updateInteractionResponseOptions($interaction);

                    //costly...
                    //then simulate get+save response data to filter affected response variables
                    $this->service->saveInteractionResponse($interaction, $this->service->getInteractionResponseData($interaction));
                }

                //@todo no longer support manual choice sorting in model

                $saved = true;
            }
        }

        $response = array(
            'saved' => $saved,
            'reloadResponse' => $reloadResponse
        );

        if(!empty($newGraphicObject)){
            $response['newGraphicObject'] = $newGraphicObject;
        }
        if(!empty($newMediaObject)){
            $response['newMediaObject'] = $newMediaObject;
        }

        echo json_encode($response);
    }

    /**
     * Get an array containing the information related to the image with the given url
     * 
     * @param string $filePath
     * @return array
     */
    private function getMediaProperties($filePath, $mediaTypes = array()){

        $returnValue = array();

        if(!empty($filePath)){

            if(!preg_match("/^http/", $filePath)){
                if($this->hasSessionAttribute('uri') && $this->hasSessionAttribute('classUri')){
                    $itemService = taoItems_models_classes_ItemsService::singleton();
                    $classUri = tao_helpers_Uri::decode($this->getSessionAttribute('classUri'));
                    if($itemService->isItemClass(new core_kernel_classes_Class($classUri))){
                        $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getSessionAttribute('uri')));
                        if(!is_null($item)){
                            $folder = $itemService->getItemFolder($item);
                            $filePath = tao_helpers_File::concat(array($folder, $filePath));
                        }
                    }
                }
            }



            if(preg_match('/^http(s)?:\/\/(www\.)?youtu(\.)?be/i', $filePath)){//hack to enable youtube video:
                $returnValue['mime'] = 'video/youtube';
            }else if(@fclose(@fopen($filePath, "r"))){//check if file remotely exists, might be improved with cURL
                $mimeType = tao_helpers_File::getMimeType($filePath);
                common_Logger::d('mime '.$mimeType);

                $validMediaType = array(
                    'image' => array(
                        'image/png',
                        'image/jpeg',
                        'image/bmp',
                        'image/gif',
                        'image/vnd.microsoft.icon',
                        'image/tiff',
                    ),
                    'audio' => array(
                        'audio/mpeg'
                    ),
                    'video' => array(
                        'video/mp4', //(H.264 + AAC) for ie8, etc.
                        'video/webm', //(VP8 + Vorbis) for ie9, ff, chrome, android, opera
                        'video/ogg'
                    )
                );

                $validTypes = array();
                foreach($mediaTypes as $type){
                    if(isset($validMediaType[$type])){
                        $validTypes = array_merge($validTypes, $validMediaType[$type]);
                    }
                }

                if(in_array($mimeType, $validTypes)){
                    $returnValue['mime'] = $mimeType;
                }
            }
        }else{
            throw new InvalidArgumentException('file path must not be empty');
        }

        return $returnValue;
    }

    /**
     * Save choice data and properties
     * 
     * @access public
     */
    public function saveChoice(){
        $choice = $this->getCurrentChoice();

        $myForm = $choice->toForm();
        $saved = false;
        $identifierUpdated = false;
        $errorMessage = '';

        if($myForm->isSubmited()){
            if($myForm->isValid()){

                $values = $myForm->getValues();
                unset($values['choiceSerial']); //choiceSerial to be deleted since only used to get the choice qti object

                if(isset($values['choiceIdentifier'])){
                    if($values['choiceIdentifier'] != $choice->getIdentifier()){
                        $this->service->setIdentifier($choice, $values['choiceIdentifier']);
                        $identifierUpdated = true;
                    }
                    unset($values['choiceIdentifier']);
                }

                if(isset($values['data'])){
                    $this->service->setChoiceContent($choice, $this->getPostedData('data'));
                    unset($values['data']);
                }

                if($choice instanceof taoQTI_models_classes_QTI_choice_GapImg){
                    //for graphic interactions:
                    $newGraphicObject = array();
                    if(isset($values['object_width'])){
                        if(intval($values['object_width'])){
                            $newGraphicObject['width'] = floatval($values['object_width']);
                        }
                        unset($values['object_width']);
                    }
                    if(isset($values['object_height'])){
                        if(intval($values['object_height'])){
                            $newGraphicObject['height'] = floatval($values['object_height']);
                        }
                        unset($values['object_height']);
                    }
                    if(isset($values['object_data'])){

                        // $oldObject = $choice->getObject();
                        //get mime type
                        $imageFilePath = trim($values['object_data']);

                        $imgProperties = $this->getMediaProperties($imageFilePath, array('image'));
                        if(!empty($imgProperties)){
                            $newGraphicObject['data'] = $imageFilePath;
                            $newGraphicObject['type'] = $imgProperties['mime'];
                        }else{
                            $errorMessage = __('invalid image mime type for the image file '.$imageFilePath);
                        }
                        unset($values['object_data']);
                    }
                    $choice->getObject()->setAttributes($newGraphicObject);
                }

                $this->service->setOptions($choice, $values);

                $saved = true;
            }
        }

        echo json_encode(array(
            'saved' => $saved,
            'choiceSerial' => $choice->getSerial(),
            'identifierUpdated' => $identifierUpdated,
            'reload' => false, //@see requireChoicesUpdate()
            'errorMessage' => (string) $errorMessage
        ));
    }

    /**
     * Display the response processing form
     * 
     * @access public
     */
    public function editResponseProcessing(){

        $item = $this->getCurrentItem();

        $formContainer = new taoQTI_actions_QTIform_ResponseProcessing($item);
        $myForm = $formContainer->getForm();

        $this->setData('form', $myForm->render());
        $processingType = $formContainer->getProcessingType();

        $warningMessage = '';
        if($processingType == 'custom'){
            $warningMessage = __('The custom response processing type is currently not fully supported in this tool. Removing interactions or choices is not recommended.');
        }

        $this->setData('warningMessage', $warningMessage);
        $this->setView('QTIAuthoring/form_response_processing.tpl');
    }

    /**
     * Save the response processing mode of the QTI item
     * 
     * @access public
     */
    public function saveItemResponseProcessing(){

        $item = $this->getCurrentItem();
        $responseProcessingType = tao_helpers_Uri::decode($this->getRequestParameter('responseProcessingType'));
        $customRule = $this->getRequestParameter('customRule');

        $saved = $this->service->setResponseProcessing($item, $responseProcessingType, $customRule);

        echo json_encode(array(
            'saved' => $saved,
            'responseMode' => taoQTI_helpers_qti_InteractionAuthoring::isResponseMappingMode($responseProcessingType)
        ));
    }

    /**
     * Save the reponse processing of the interaction
     * 
     * @access public
     */
    public function saveInteractionResponseProcessing(){
        $response = $this->getCurrentResponse();
        $rp = $this->getCurrentResponseProcessing();

        if(!is_null($response) && !is_null($rp)){
            if($rp instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){
                $saved = false;
                $setResponseMappingMode = false;
                $templateHasChanged = false;
                if($this->hasRequestParameter('processingTemplate')){
                    $processingTemplate = tao_helpers_Uri::decode($this->getRequestParameter('processingTemplate'));
                    if($rp->getTemplate($response) != $processingTemplate){
                        $templateHasChanged = true;
                    }
                    $saved = $rp->setTemplate($response, $processingTemplate);
                    if($saved){
                        $setResponseMappingMode = taoQTI_helpers_qti_InteractionAuthoring::isResponseMappingMode($processingTemplate);
                    }
                }

                if($templateHasChanged){
                    if(!$setResponseMappingMode){
                        //when response processing mode switches from "map" to "correct", need to update the associated fedback rules
                        //update: not sure if needed because the choice of resp processing is unrealted to existing correct and map values declaration in response declaration
                        /*
                          foreach($response->getFeedbackRules as $rule){
                          $rule->setCondition($response, 'correct');
                          }
                         */
                    }
                }

                echo json_encode(array(
                    'saved' => $saved,
                    'setResponseMappingMode' => $setResponseMappingMode,
                    'hasChanged' => $templateHasChanged
                ));
            }elseif($rp instanceof taoQTI_models_classes_QTI_response_Composite){
                $currentIRP = $rp->getInteractionResponseProcessing($response);
                $currentClass = get_class($currentIRP);
                $saved = false;
                $classID = $currentClass::CLASS_ID;

                if($this->hasRequestParameter('interactionResponseProcessing')){
                    $classID = $this->getRequestParameter('interactionResponseProcessing');
                    if($currentClass::CLASS_ID != $classID){
                        $newIRP = taoQTI_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing::create(
                                        $classID, $response, $this->item
                        );
                        $rp->replace($newIRP);
                        $saved = true;
                    }
                }
                echo json_encode(array(
                    'saved' => $saved,
                    'setResponseOptionsMode' => $classID
                ));
            }
        }
    }

    /**
     * Display the reponse mapping options form
     * The reponse processing template must be map or areamap
     * 
     * @access public
     */
    public function editMappingOptions(){
        $response = $this->getCurrentResponse();

        $formContainer = new taoQTI_actions_QTIform_Mapping($response);

        $this->setData('form', $formContainer->getForm()->render());
        $this->setView('QTIAuthoring/form_response_mapping.tpl');
    }

    /**
     * Save interaction responses (choice, correct, score)
     * 
     * @access public
     */
    public function saveResponse(){

        $saved = false;

        //get the response from the interaction:
        $interaction = $this->getCurrentInteraction();

        if($this->hasRequestParameter('responseData')){
            $responseData = $this->getRequestParameter('responseData');
            $saved = $this->service->saveInteractionResponse($interaction, $responseData);
        }

        echo json_encode(array(
            'saved' => $saved
        ));
    }

    /**
     * Save interaction reponses properties (baseType, ordered, etc.)
     * 
     * @access public
     */
    public function saveResponseProperties(){

        $saved = false;
        $response = $this->getCurrentResponse();

        if(!is_null($response)){

            if($this->hasRequestParameter('baseType')){
                if($this->hasRequestParameter('baseType')){
                    $this->service->editOptions($response, array('baseType' => $this->getRequestParameter('baseType')));
                    $saved = true;
                }

                if($this->hasRequestParameter('ordered')){
                    if(intval($this->getRequestParameter('ordered')) == 1){
                        $this->service->editOptions($response, array('cardinality' => 'ordered'));
                    }else{
                        //reset the cardinality:
                        $parentInteraction = $response->getAssociatedInteraction();
                        if(!is_null($parentInteraction)){
                            //relatedItem->interaction->response
                            $this->service->editOptions($response, array('cardinality' => $parentInteraction->getCardinality()));
                            $parentInteraction = null; //destroy it!
                        }else{
                            common_Logger::d('cannot find the parent interaction of the response', array('QTI'));
                        }
                    }
                    $saved = true;
                }
            }
        }

        echo json_encode(array(
            'saved' => $saved,
        ));
    }

    /**
     * Save response coding options
     * 
     * @access public
     */
    public function saveResponseCodingOptions(){

        $interaction = $this->getCurrentInteraction();
        $rp = $this->getCurrentResponseProcessing();
        $form = null;

        // cases
        if($rp instanceof taoQTI_models_classes_QTI_response_TemplatesDriven){
            $form = 'template';
        }elseif($rp instanceof taoQTI_models_classes_QTI_response_Composite){
            $irp = $rp->getInteractionResponseProcessing($interaction->getResponse());
            if($irp instanceof taoQTI_models_classes_QTI_response_interactionResponseProcessing_None){
                $form = 'manual';
            }elseif(in_array(get_class($irp), array(
                        'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate',
                        'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate',
                        'taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate'))){

                $form = 'template';
            }
        }

        if($form == 'template'){
            $response = $interaction->getResponse();
            $mappingOptions = $_POST;

            $this->service->setMappingOptions($response, $mappingOptions);
            $saved = true;

            echo json_encode(array(
                'saved' => $saved
            ));
        }elseif($form == 'manual'){
            $irp = $rp->getInteractionResponseProcessing($interaction->getResponse());
            $saved = false;
            $outcome = $this->getCurrentOutcome();

            // set guidelines
            if($this->hasRequestParameter('guidelines')){
                $values = array(
                    'interpretation' => $this->getRequestParameter('guidelines')
                );
                $saved = $this->service->editOptions($outcome, $values) || $saved;
            }

            // set correct answer
            if($this->hasRequestParameter('correct')){
                $responseData = array(array(
                        'choice1' => $this->getRequestParameter('correct'),
                        'correct' => 'yes'
                ));
                $saved = $this->service->saveInteractionResponse($this->getCurrentInteraction(), $responseData) || $saved;
            }

            // set guidelines
            if($this->hasRequestParameter('defaultValue')){
                $irp->setDefaultValue($this->getRequestParameter('defaultValue'));
                $saved = true;
            }

            // set scale
            if($this->hasRequestParameter('scaletype')){

                if(strlen(trim($this->getRequestParameter('scaletype'))) > 0){
                    $uri = tao_helpers_Uri::decode($this->getRequestParameter('scaletype'));
                    $scale = taoItems_models_classes_Scale_Scale::createByClass($uri);

                    if($this->hasRequestParameter('min')){
                        $scale->lowerBound = (floatval($this->getRequestParameter('min')));
                    }
                    if($this->hasRequestParameter('max')){
                        $scale->upperBound = (floatval($this->getRequestParameter('max')));
                    }
                    if($this->hasRequestParameter('dist')){
                        $scale->distance = (floatval($this->getRequestParameter('dist')));
                    }
                    $outcome->setScale($scale);
                    $saved = true;
                }else{
                    $outcome->removeScale();
                    $saved = true;
                }
            }

            echo json_encode(array(
                'saved' => $saved
            ));
        }else{
            echo json_encode(array(
                'saved' => false
            ));
        }
    }

    /**
     * Edit the interaction response:
     * 
     * @access public
     */
    public function editResponse(){

        $item = $this->getCurrentItem();
        $responseProcessing = $item->getResponseProcessing();
        $interaction = $this->getCurrentInteraction();
        $response = $this->service->getInteractionResponse($interaction);

        $displayGrid = false;
        $isResponseMappingMode = false;
        $columnModel = array();
        $responseData = array();
        $responseForms = array();
        $interactionType = strtolower($interaction->getType());

        //response options independant of processing
        $responseForm = $response->toForm();
        if(!is_null($responseForm)){
            $responseForms[] = $responseForm->render();
        }

        //set the processing mode
        $rpform = $responseProcessing->getForm($response);
        if(!is_null($rpform)){
            $responseForms[] = $rpform->render();
        }

        $feedbackRules = array();
        foreach($response->getFeedbackRules() as $serial => $rule){
            $feedbackRules[$serial] = $this->renderFeedbackRuleForm($rule);
        }

        $data = array(
            'ok' => true,
            'interactionType' => $interactionType,
            'maxChoices' => intval($interaction->getCardinality(true)),
            'forms' => $responseForms,
            'feedbackForms' => $feedbackRules
        );

        //proccessing related form
        foreach(taoQTI_helpers_qti_InteractionAuthoring::getIRPData($item, $interaction) as $key => $value){
            if(isset($data[$key]) && is_array($data[$key])){
                foreach($value as $v){
                    $data[$key][] = $v;
                }
            }else{
                $data[$key] = $value;
            }
        }
        $data['responseForm'] = implode('', $data['forms']);

        echo json_encode($data);
    }

    /**
     * Display the css manager interface
     * 
     * @access public
     */
    public function manageStyleSheets(){
        //create upload form:
        $item = $this->getCurrentItem();
        $formContainer = new taoQTI_actions_QTIform_CSSuploader($item, $this->getRequestParameter('itemUri'));
        $myForm = $formContainer->getForm();

        if($myForm->isSubmited()){
            if($myForm->isValid()){
                $data = $myForm->getValues();

                if(isset($data['css_import']['uploaded_file'])){
                    //get the file and store it in the proper location:
                    $baseName = basename($data['css_import']['uploaded_file']);

                    $fileData = $this->getCurrentStyleSheet($baseName);

                    if(!empty($fileData)){
                        tao_helpers_File::move($data['css_import']['uploaded_file'], $fileData['path']);

                        $stylesheet = new taoQTI_models_classes_QTI_Stylesheet(array(
                            'title' => empty($data['title']) ? $data['css_import']['name'] : $data['title'],
                            'href' => $fileData['href'],
                            'type' => 'text/css',
                            'media' => 'screen'//@TODO:default to "screen" make suggestion of other devises such as "handheld" when mobile ready
                        ));
                        $item->addStylesheet($stylesheet);
                    }
                }
            }
        }

        $cssFiles = array();
        foreach($item->getStyleSheets() as $css){
            $cssFiles[] = array(
                'href' => $css->attr('href'),
                'title' => $css->attr('title'),
                'downloadUrl' => _url('getStyleSheet', null, null, array(
                    'itemSerial' => tao_helpers_Uri::encode($item->getSerial()),
                    'itemUri' => tao_helpers_Uri::encode($this->getCurrentItemResource()->getUri()),
                    'css_href' => $css->attr('href')
                ))
            );
        }

        $this->setData('formTitle', __('Manage item content'));
        $this->setData('myForm', $myForm->render());
        $this->setData('cssFiles', $cssFiles);
        $this->setView('QTIAuthoring/css_manager.tpl');
    }

    /**
     * Delete a style sheet
     * 
     * @access public
     */
    public function deleteStyleSheet(){

        $deleted = false;

        $fileData = $this->getCurrentStyleSheet();

        //get the full path of the file and unlink the file:
        if(!empty($fileData)){
            tao_helpers_File::remove($fileData['path']);
            $item = $this->getCurrentItem();
            $files = $item->getStylesheets();
            foreach($files as $file){
                if($file->attr('href') == $fileData['href']){
                    $item->removeStylesheet($file);
                    $deleted = true;
                    break;
                }
            }
        }

        echo json_encode(array('deleted' => $deleted));
    }

    /**
     * Get a style sheet
     * 
     * @access public
     */
    public function getStyleSheet(){
        $fileData = $this->getCurrentStyleSheet();
        if(!empty($fileData)){
            $fileName = basename($fileData['path']);

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: public");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Description: File Transfer");
            header("Content-Type: text/css");
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($fileData['path']));

            echo file_get_contents($fileData['path']);
        }else{
            throw new common_Exception('The style file cannot be found');
        }
    }

    /**
     * Get a data of a stylesheet
     * 
     * @access public
     * @param basename
     * @return array
     */
    public function getCurrentStyleSheet($baseName = ''){
        $returnValue = array();
        $itemResource = $this->getCurrentItemResource();
        $basePath = taoItems_models_classes_ItemsService::singleton()->getItemFolder($itemResource);
        $baseWWW = taoItems_models_classes_ItemsService::singleton()->getRuntimeFolder($itemResource);

        if(!empty($baseName)){
            //creation mode:
            $css_href = 'style/'.$baseName;

            $returnValue = array(
                'href' => $css_href,
                'type' => 'text/css',
                'title' => $baseName,
                'path' => $basePath.'/'.$css_href,
                'hrefAbsolute' => $baseWWW.'/'.$css_href
            );
        }else{
            //get mode:
            $css_href = $this->getRequestParameter('css_href');
            if(!empty($css_href)){
                $files = $this->getCurrentItem()->getStylesheets();
                foreach($files as $file){
                    if($file->attr('href') == $css_href){
                        $returnValue = $file->getAttributeValues();
                        $returnValue['path'] = $basePath.'/'.$css_href;
                        $returnValue['hrefAbsolute'] = $baseWWW.'/'.$css_href;
                        break;
                    }
                }
            }
        }

        return $returnValue;
    }

    /**
     * Add a object into the item body
     * 
     * @deprecated use addElmeent isntead
     * @access public
     */
    public function addObject(){
        $item = $this->getCurrentItem();
        $object = $this->service->addObject($item, $this->getPostedItemData());
        $itemData = $this->service->getItemData($item);
        $added = true;

        echo json_encode(array(
            'added' => $added,
            'objectSerial' => $object->getSerial(),
            'itemData' => $itemData
        ));
    }

    public function addElement(){

        $added = false;
        $errorMessage = '';
        $serial = '';
        $body = '';

        $elementContainer = $this->getCurrentElement();
        if($this->hasRequestParameter('newType') && !is_null($elementContainer)){

            $newType = $this->getRequestParameter('newType');

            $filter = 'blockStatic';
            if($elementContainer instanceof taoQTI_models_classes_QTI_interaction_Prompt){
                $filter = 'inlineStatic';
            }
            $newElement = $this->service->addElement($elementContainer, $newType, $this->getPostedData('body', true, $filter));
            if(!is_null($newElement)){
                $serial = $newElement->getSerial();
                $body = $this->service->getData($elementContainer);
                $added = true;
            }else{
                $errorMessage = 'cannot add new element';
            }
        }else{
            $errorMessage = 'missing required parameters';
            throw new common_Exception('missing required parameters');
        }

        echo json_encode(array(
            'added' => $added,
            'errorMessage' => $errorMessage,
            'serial' => $serial,
            'body' => $body,
        ));
    }

    /**
     * Delete objects
     * 
     * @access public
     */
    public function deleteObjects(){

        $deleted = false;

        $objectSerials = array();
        if($this->hasRequestParameter('objectSerials')){
            $objectSerials = $this->getRequestParameter('objectSerials');
        }
        if(empty($objectSerials)){
            throw new common_Exception('no object ids found to be deleted');
        }else{

            $item = $this->getCurrentItem();
            $deleteCount = 0;

            //delete objects:
            foreach($objectSerials as $objectSerial){
                $this->service->deleteObject($item, $objectSerial);
                $deleteCount++;
            }

            if($deleteCount == count($objectSerials)){
                $deleted = true;
            }
        }

        echo json_encode(array(
            'deleted' => $deleted
        ));
    }

    public function saveData(){
        $filterModel = ($this->getCurrentElement() instanceof taoQTI_models_classes_QTI_interaction_Prompt) ? 'inlineStatic' : 'blockStatic';
        $data = $this->getPostedData('data', true, $filterModel);
        $saved = $this->service->saveData($this->getCurrentElement(), $data);
        echo json_encode(array(
            'saved' => $saved
        ));
    }

    protected function getCurrentElement(){

        if(is_null($this->currentElement)){

            if($this->hasRequestParameter('serial') && $this->hasRequestParameter('type')){

                $type = $this->getRequestParameter('type');
                $serial = $this->getRequestParameter('serial');

                if($type == 'item'){
                    $this->currentElement = $this->getCurrentItem();
                }else{
                    $found = null;
                    $idElts = $this->getCurrentItem()->getIdentifiedElements()->get();
                    foreach($idElts as $id => $elts){
                        if(isset($elts[$serial])){
                            $found = $elts[$serial];
                            break;
                        }
                    }

                    if($type === 'prompt'){//quick hack to distinguish data from interaction body and prompt
                        $this->currentElement = $found->getPromptObject();
                    }else{
                        $this->currentElement = $found;
                    }
                    if(is_null($this->currentElement)){
                        throw new taoQTI_models_classes_QTI_QtiModelException('no element found with the seral '.$serial);
                    }
                }
            }
        }

        return $this->currentElement;
    }

    protected function getCurrentObject(){

        if(is_null($this->object)){
            if($this->hasRequestParameter('objectSerial')){
                $objectSerial = $this->getRequestParameter('objectSerial');
                $elements = $this->getCurrentElement()->getBody()->getElements();
                if(isset($elements[$objectSerial])){
                    $this->object = $elements[$objectSerial];
                }else{
                    throw new InvalidArgumentException('missing object serial');
                }
            }
        }

        if(is_null($this->object)){
            throw new common_Exception('Object not found');
        }

        return $this->object;
    }

    public function editObject(){

        $myObject = $this->getCurrentObject();
        $errorMessage = '';
        $saved = false;

        if($this->hasRequestParameter('width')){
            $width = $this->getRequestParameter('width');
            if(intval($width) == 0){
                $myObject->removeAttributeValue('width'); //authorize empty value for optional attr
            }else{
                $myObject->attr('width', intval($width));
            }
        }

        if($this->hasRequestParameter('height')){
            $height = $this->getRequestParameter('height');
            if(intval($height) == 0){
                $myObject->removeAttributeValue('height'); //authorize empty value for optional attr
            }else{
                $myObject->attr('height', intval($height));
            }
        }

        if($this->hasRequestParameter('data')){

            $mediaFilePath = trim($this->getRequestParameter('data'));
            $mediaProperties = $this->getMediaProperties($mediaFilePath, array('audio', 'video'));
            if(!empty($mediaProperties)){
                $myObject->attr('data', $mediaFilePath);
                $myObject->attr('type', $mediaProperties['mime']);
                $saved = true;
            }else{
                $errorMessage = __('invalid media mime type');
            }
        }

        $formContainer = new taoQTI_actions_QTIform_EditObject($myObject);
        $myForm = $formContainer->getForm();
        $mediaFilePath = $myObject->getAttributeValue('data');
        $mediaFileWidth = (intval($myObject->getAttributeValue('width')) > 0) ? $myObject->getAttributeValue('width') : '';
        $mediaFileHeight = (intval($myObject->getAttributeValue('height')) > 0) ? $myObject->getAttributeValue('height') : '';
        $mediaFileType = trim($myObject->getAttributeValue('type'));
        $mediaFileType = empty($mediaFileType) ? '' : $mediaFileType;

        $renderer = new Renderer(DIR_VIEWS.'templates/QTIAuthoring/form_object.tpl');
        $renderer->setData('errorMessage', $errorMessage);
        $renderer->setData('objectSerial', $myObject->getSerial());
        $renderer->setData('form', $myForm->render());
        $renderer->setData('mediaFilePath', $mediaFilePath);
        $renderer->setData('mediaFileWidth', $mediaFileWidth);
        $renderer->setData('mediaFileHeight', $mediaFileHeight);
        $renderer->setData('mediaFileType', $mediaFileType);

        echo json_encode(array(
            'title' => __('Edit object'),
            'objectSerial' => $myObject->getSerial(),
            'html' => $renderer->render(),
            'saved' => $saved,
            'errorMessage' => $errorMessage,
            'media' => array(
                'data' => $mediaFilePath,
                'width' => $mediaFileWidth,
                'height' => $mediaFileHeight,
                'type' => $mediaFileType
            )
        ));
    }

    /**
     * Check if an identifier is used by any of a QTI object within the current QTI item
     * 
     * @access public
     */
    public function isIdentifierUsed(){
        $used = false;
        if($this->hasRequestParameter('identifier')){
            $used = $this->service->isIdentifierUsed($this->getCurrentItem(), $this->getRequestParameter('identifier'));
        }
        echo json_encode(array(
            'used' => $used
        ));
    }

    public function __destruct(){
        $this->qtiService->saveItemToSession($this->item);
    }

    public function deleteElements(){
        $deleted = false;
        $elementSerials = $this->getRequestParameter('elements');
        if(is_array($elementSerials)){
            foreach($elementSerials as $serial){
                if(strpos($serial, 'interaction') === 0){
                    $this->service->deleteInteraction($this->getCurrentElement(), $serial);
                }else{
                    $this->getCurrentElement()->getBody()->removeElement($serial);
                }
            }
            $deleted = true;
        }
        echo json_encode(array('deleted' => $deleted));
    }

    public function editMath(){

        $math = $this->getCurrentObject(); //use "objectSerial" to pass math element instance 
        $form = $math->toForm();

        $mathML = $math->getMathML();
        $tex = $math->getAnnotation('latex');
        $display = $math->attr('display');
        if(empty($display)){
            $display = 'inline';
        }

        $renderer = new Renderer(DIR_VIEWS.'templates/QTIAuthoring/form_math.tpl');
        $renderer->setData('errorMessage', 'Math expression cannot be edited properly because MathJax library is missing. To enable math expression in TAO, please install MathJax library following the instructions <a href="http://forge.taotesting.com/projects/tao/wiki/Enable_math">here</a>.');
        $renderer->setData('objectSerial', $math->getSerial());
        $renderer->setData('form', $form->render());

        $textProceed = __('Proceed anyway?');
        $br = '<br/>';

        echo json_encode(array(
            'title' => __('Edit math'),
            'objectSerial' => $math->getSerial(),
            'html' => $renderer->render(),
            'math' => array(
                'mathML' => $mathML,
                'tex' => $tex,
                'display' => $display
            ),
            'tips' => array(
                'tex' => __('Edit LaTeX Code.').' '.__('e.g.').' x = {-b \pm \sqrt{b^2-4ac} \over 2a}',
                'texSwitch' => __('Switch to LaTeX editing mode'),
                'texWarning' => __('Editing LaTeX Code will automatically update and overwrite MathML.').$br.$br.$textProceed,
                'math' => __('Edit MathML.'),
                'mathSwitch' => __('Switch to MathML editing mode'),
                'mathWarning' => __('No MathML conversion to LaTeX is currently available.').$br.__('Editing MathML will therefore not update existing Latex code but delete it instead.').$br.$br.$textProceed,
            )
        ));
    }

    public function saveMath(){

        $saved = false;
        $math = $this->getCurrentObject(); //use "objectSerial" to pass math element instance 
        $display = '';
        $errorMessage = '';

        if($this->hasRequestParameter('mathML')){

            $mathML = $_POST['mathML'];
            $math->setMathML($mathML);

            if(isset($_POST['tex'])){
                $tex = $_POST['tex'];
                if(empty($tex)){
                    $math->removeAnnotation('latex');
                }else{
                    $math->setAnnotation('latex', $tex);
                }
                //@todo: what about the other annotations if exists?
            }

            $display = $this->getRequestParameter('display');
            if($display == 'block'){
                $math->attr('display', 'block');
            }else{
                $display = 'inline';
                $math->removeAttributeValue('display'); //considered null
            }

            $saved = true;
        }

        echo json_encode(array(
            'saved' => $saved,
            'display' => $display,
            'errorMessage' => $errorMessage,
        ));
    }

    public function getCurrentFeedbackRule(){
        $returnValue = null;

        if($this->hasRequestParameter('ruleSerial')){
            $ruleSerial = $this->getRequestParameter('ruleSerial');
            $returnValue = $this->getCurrentResponse()->getFeedbackRule($ruleSerial);
        }else{
            throw new common_Exception('missing required exception "ruleSerial"');
        }

        return $returnValue;
    }

    public function addFeedbackRule(){
        $rule = $this->service->createFeedbackRule($this->getCurrentResponse());
        echo json_encode(array(
            'added' => true,
            'data' => $rule->toArray(),
            'form' => $this->renderFeedbackRuleForm($rule)
        ));
    }

    public function editFeedbackRules(){

        $data = array();

        $rules = $this->getCurrentResponse()->getFeedbackRules();
        foreach($rules as $serial => $rule){
            $data[$serial] = array(
                'data' => $rule->toArray(),
                'form' => $this->renderFeedbackRuleForm($rule)
            );
        }

        echo json_encode($data);
    }

    public function editFeedbackRule(){
        $rule = $this->getCurrentFeedbackRule();
        echo json_encode(array(
            'data' => $rule->toArray(),
            'form' => $this->renderFeedbackRuleForm($rule)
        ));
    }

    protected function renderFeedbackRuleForm(taoQTI_models_classes_QTI_response_SimpleFeedbackRule $rule){

        $renderer = new Renderer(DIR_VIEWS.'templates/QTIAuthoring/form_response_feedback_rule.tpl');
        foreach($rule->toArray() as $k => $v){
            $renderer->setData($k, $v);
        }
        $renderer->setData('conditions', array(
            'correct' => __('response is correct'),
            'lt' => __('score is less than'),
            'lte' => __('score is less than or equal to'),
            'equal' => __('score is equal to'),
            'gte' => __('score is greater than or equal to'),
            'gt' => __('score is greater than')
        ));
        return $renderer->render();
    }

    public function saveFeedbackRules(){
        $saved = false;
        $response = $this->getCurrentResponse();
        if($this->hasRequestParameter('rules')){
            $rules = $this->getRequestParameter('rules');
            foreach($rules as $serial => $prop){
                $rule = $response->getFeedbackRule($serial);
                if(!is_null($rule)){
                    $rule->setCondition($response, strval($prop['condition']), floatval($prop['value']));
                }else{
                    throw new common_Exception('cannot find the feedback rule in response');
                }
            }
            $saved = true;
        }
        echo json_encode(array('saved' => $saved));
    }

    public function deleteFeedbackRule(){
        $deleted = $this->service->deleteFeedbackRule($this->getCurrentResponse(), $this->getCurrentFeedbackRule());
        echo json_encode(array('deleted' => $deleted));
    }

    public function addFeedbackRuleElse(){
        $rule = $this->getCurrentFeedbackRule();
        $added = !is_null($this->service->addFeedbackRuleElse($rule));
        echo json_encode(array(
            'added' => $added,
            'data' => $rule->toArray(),
            'form' => $this->renderFeedbackRuleForm($rule)
        ));
    }

    public function deleteFeedbackRuleElse(){
        $rule = $this->getCurrentFeedbackRule();
        $deleted = $this->service->deleteFeedbackRuleElse($rule);
        echo json_encode(array(
            'deleted' => $deleted,
            'data' => $rule->toArray(),
            'form' => $this->renderFeedbackRuleForm($rule)
        ));
    }

    public function getCurrentModalFeedback(){
        $returnValue = null;
        if($this->hasRequestParameter('feedbackSerial')){
            $serial = $this->getRequestParameter('feedbackSerial');
            $feedback = $this->getCurrentItem()->getModalFeedback($serial);
            if(!is_null($feedback)){
                $returnValue = $feedback;
                $this->setData('feedbackSerial', $feedback->getSerial());
                $this->setData('form', $feedback->toForm()->render());
            }else{
                throw new common_Exception('no feedback found for the serial : '.$serial);
            }
        }
        return $returnValue;
    }

    public function editModalFeedback(){
        $feedback = $this->getCurrentModalFeedback();
        $this->setData('feedbackSerial', $feedback->getSerial());
        $this->setData('form', $feedback->toForm()->render());
        $this->setView('QTIAuthoring/form_feedback_modal.tpl');
    }

    public function saveModalFeedback(){
        $feedback = $this->getCurrentModalFeedback();
        if($this->hasRequestParameter('title')){
            $feedback->attr('title', (string) $this->getRequestParameter('title'));
        }
        if($this->hasRequestParameter('data')){
            $body = $this->service->restorePlaceholders($feedback, $this->getPostedData('data', true));
            $this->service->saveData($feedback, $body);
        }
        echo json_encode(array('saved' => true));
    }

    public function editImage(){
        $imgForm = new taoQTI_actions_QTIform_Image();
        $renderer = new Renderer(DIR_VIEWS.'templates/QTIAuthoring/form_image.tpl');
        $renderer->setData('errorMessage', '');
        $renderer->setData('form', $imgForm->getForm()->render());

        echo json_encode(array(
            'title' => __('Insert Image'),
            'html' => $renderer->render()
        ));
    }

    public function saveChoiceIdentifier(){
        $saved = false;
        $identifier = trim($this->getRequestParameter('identifier'));
        if(!empty($identifier)){
            $saved = $this->service->setIdentifier($this->getCurrentChoice(), $identifier);
        }
        echo json_encode(array('saved' => $saved));
    }

}