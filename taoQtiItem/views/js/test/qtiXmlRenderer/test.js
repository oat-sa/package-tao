define([
    'taoQtiItem/qtiItem/core/Loader',
    'taoQtiItem/qtiItem/core/Element',
    'taoQtiItem/qtiXmlRenderer/renderers/Renderer',
    'json!taoQtiItem/test/samples/json/space-shuttle-m.json',
    'json!taoQtiItem/test/samples/json/airports.json',
    'json!taoQtiItem/test/samples/json/edinburgh.json',
    'json!taoQtiItem/test/samples/json/choice-custom.json',
    'json!taoQtiItem/test/samples/json/choice-feedback.json',
    'json!taoQtiItem/test/samples/json/choice-feedback-complex.json',
    'json!taoQtiItem/test/samples/json/rivals.json'
], function(
    Loader,
    Element,
    Renderer,
    item_shuttle,
    item_airports,
    item_edinburgh,
    item_custom,
    item_feedback,
    item_feedback_complex,
    item_rivals){

    function minXml(xml){
        return xml
            .replace(/\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>/g, "")
            .replace(/[ \r\n\t]+xmlns/g, ' xmlns')
            .replace(/[\n\s]+/g, " ")
            .replace(/>[\n\s]?</g, "><")
            .trim();
    }

    var items = [
        {
            data : item_shuttle,
            comment : 'choice',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="space-shuttle-30-years-of-adventure" title="The Space Shuttle, 30 years of adventure" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.0" ><responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="identifier" ><correctResponse><value><![CDATA[Atlantis]]></value><value><![CDATA[Pathfinder]]></value></correctResponse></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" ><defaultValue><value>0</value></defaultValue></outcomeDeclaration><itemBody><p id="stimulus"> During more than 30 years, the American Space Shuttle transported 355 astronauts in space over 135 orbital launchings from Cap Canaveral, Florida. This 2046-ton Behemoth traveled 870 million kilometers around the Earth from 12th of April 1981 to 21st of July 2011. The National Aeronautics and Space Administration (NASA) built 5 Space Shuttles: Columbia, Challenger, Discovery, Atlantis and Endeavour. </p><choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="2" minChoices="1" id="interaction" ><prompt >Which was the last Space Shuttle going into space during the STS-135 mission in July 2011?</prompt><simpleChoice identifier="Discovery" fixed="false" >Discovery</simpleChoice><simpleChoice identifier="Challenger" fixed="false" >Challenger</simpleChoice><simpleChoice identifier="Pathfinder" fixed="false" >Pathfinder</simpleChoice><simpleChoice identifier="Atlantis" fixed="false" >Atlantis</simpleChoice><simpleChoice identifier="Endeavour" fixed="false" >Endeavour</simpleChoice></choiceInteraction></itemBody><responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct"/></assessmentItem>'
        },
        {
            data : item_airports,
            comment : 'graphic interaction',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="hotspot" title="UK Airports (Take 1)" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.0" ><responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier" ><correctResponse><value><![CDATA[A]]></value></correctResponse></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" /><itemBody><p>The picture illustrates four of the most popular destinations for air travellers arriving in the United Kingdom: London, Manchester, Edinburgh and Glasgow.</p><hotspotInteraction responseIdentifier="RESPONSE" maxChoices="1" minChoices="0" ><prompt >Which one is Glasgow?</prompt><object data="images/ukair.png" type="image/png" width="206" height="280" >UK Map</object><hotspotChoice identifier="A" fixed="false" shape="circle" coords="77,115,8" /><hotspotChoice identifier="B" fixed="false" shape="circle" coords="118,184,8" /><hotspotChoice identifier="C" fixed="false" shape="circle" coords="150,235,8" /><hotspotChoice identifier="D" fixed="false" shape="circle" coords="96,114,8" /></hotspotInteraction></itemBody><responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct"/></assessmentItem>'
        },
        {
            data : item_edinburgh,
            comment : 'match correct rp + areamapping attributes',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="selectPoint" title="Where is Edinburgh?" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.0" ><responseDeclaration identifier="RESPONSE" cardinality="single" baseType="point" ><correctResponse><value><![CDATA[102 113]]></value></correctResponse><areaMapping defaultValue="0"><areaMapEntry shape="circle" coords="102,113,16" mappedValue="1" /></areaMapping></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" /><itemBody><selectPointInteraction responseIdentifier="RESPONSE" maxChoices="1" minChoices="0" ><prompt >Mark Edinburgh on this map of the United Kingdom.</prompt><object data="images/uk.png" type="image/png" width="196" height="280" >UK Map</object></selectPointInteraction></itemBody><responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point"/></assessmentItem>'
        },
        {
            data : item_custom,
            comment : 'composite choice + custom rp + mapping attributes',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xi="http://www.w3.org/2001/XInclude" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="RC123413" title="RC123413" label="" xml:lang="en-US" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.1.0-sprint12" ><responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier" ><correctResponse><value><![CDATA[B]]></value></correctResponse><mapping defaultValue="0"><mapEntry mapKey="A" mappedValue="-1.0" caseSensitive="false"/><mapEntry mapKey="B" mappedValue="1.0" caseSensitive="false"/><mapEntry mapKey="C" mappedValue="-1.0" caseSensitive="false"/><mapEntry mapKey="D" mappedValue="-1.0" caseSensitive="false"/></mapping></responseDeclaration><responseDeclaration identifier="RESPONSE2" cardinality="single" baseType="identifier" ><correctResponse><value><![CDATA[D2]]></value></correctResponse><mapping defaultValue="0"><mapEntry mapKey="A2" mappedValue="-1.0" caseSensitive="false"/><mapEntry mapKey="B2" mappedValue="-1.0" caseSensitive="false"/><mapEntry mapKey="C2" mappedValue="-1.0" caseSensitive="false"/><mapEntry mapKey="D2" mappedValue="1.0" caseSensitive="false"/></mapping></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" normalMaximum="2" normalMinimum="0" ><defaultValue><value>0</value></defaultValue></outcomeDeclaration><itemBody><div class="classD"><div class="classC"><h2 class="classA">Question</h2><div class="stem"><div class="classB"><h3 class="classA part-label"><strong> Sugar plum gummies lemon</strong></h3></div><div class="classB"><p id="p001">Pie donut sweet jelly. Powder dessert cotton candy pie candy canes gummies gingerbread marshmallow pie.</p></div><choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="1" minChoices="1" orientation="vertical" class="at-enable" ><prompt /><simpleChoice identifier="A" fixed="false" ><p id="p002">Wafer tart oat cake tart ice cream marzipan.</p></simpleChoice><simpleChoice identifier="B" fixed="false" ><p id="p003">(correct : +1) Topping tootsie roll halvah jelly liquorice. </p></simpleChoice><simpleChoice identifier="C" fixed="false" ><p id="p004">Tart oat cake jelly chocolate bar cheesecake candy bonbon.</p></simpleChoice><simpleChoice identifier="D" fixed="false" ><p id="p005">Ice cream wafer candy pie chocolate bar toffee. </p></simpleChoice></choiceInteraction><div class="classB"><p id="p006"> Fruitcake tiramisu sesame snaps apple pie apple pie tootsie roll tart tart <span class="s1">toffee ?</span></p></div><choiceInteraction responseIdentifier="RESPONSE2" shuffle="false" maxChoices="1" minChoices="1" orientation="vertical" class="at-enable" ><prompt /><simpleChoice identifier="A2" fixed="false" ><p id="p007">“Croissant oat cake candy canes chocolate bar.” <span class="s1">Gummi bears</span></p></simpleChoice><simpleChoice identifier="B2" fixed="false" ><p id="p008">“Pudding gingerbread gummies. It <span class="s1">apple pie jelly-o </span> sesame <span class="s1">(apple pie)</span></p></simpleChoice><simpleChoice identifier="C2" fixed="false" ><p id="p009">“Jelly beans dessert chocolate cake carrot cake pudding chupa chups cotton candy halvah lollipop. .” <span class="s1">( chupa chups)</span></p></simpleChoice><simpleChoice identifier="D2" fixed="false" ><p id="p010">(correct : +1) “Donut muffin gummies gingerbread bonbon. .” <span class="s1">(sugar plum)</span></p></simpleChoice></choiceInteraction></div></div></div></itemBody><responseProcessing><responseCondition><responseIf><not><equal toleranceMode="exact"><mapResponse identifier="RESPONSE"/><baseValue baseType="float">1</baseValue></equal></not><setOutcomeValue identifier="SCORE"><baseValue baseType="float">0.0</baseValue></setOutcomeValue></responseIf><responseElse><setOutcomeValue identifier="SCORE"><sum><variable identifier="SCORE"/><baseValue baseType="float">1.0</baseValue></sum></setOutcomeValue><responseCondition><responseIf><equal toleranceMode="exact"><mapResponse identifier="RESPONSE2"/><baseValue baseType="float">1</baseValue></equal><setOutcomeValue identifier="SCORE"><sum><variable identifier="SCORE"/><baseValue baseType="float">1.0</baseValue></sum></setOutcomeValue></responseIf></responseCondition></responseElse></responseCondition></responseProcessing></assessmentItem>'
        },
        {
            data : item_rivals,
            comment : 'match + custom rp + mapping rp',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="associate" title="Shakespearian Rivals" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.0" ><responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="pair" ><correctResponse><value><![CDATA[A P]]></value><value><![CDATA[C M]]></value><value><![CDATA[D L]]></value></correctResponse><mapping defaultValue="0"><mapEntry mapKey="A P" mappedValue="2" caseSensitive="false"/><mapEntry mapKey="C M" mappedValue="1" caseSensitive="false"/><mapEntry mapKey="D L" mappedValue="1" caseSensitive="false"/></mapping></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" /><itemBody><associateInteraction responseIdentifier="RESPONSE" shuffle="false" maxAssociations="3" minAssociations="0" ><prompt >Hidden in this list of characters from famous Shakespeare plays are three pairs of rivals. Can you match each character to his adversary?</prompt><simpleAssociableChoice identifier="A" fixed="false" matchMax="1" matchMin="0" >Antonio</simpleAssociableChoice><simpleAssociableChoice identifier="C" fixed="false" matchMax="1" matchMin="0" >Capulet</simpleAssociableChoice><simpleAssociableChoice identifier="D" fixed="false" matchMax="1" matchMin="0" >Demetrius</simpleAssociableChoice><simpleAssociableChoice identifier="L" fixed="false" matchMax="1" matchMin="0" >Lysander</simpleAssociableChoice><simpleAssociableChoice identifier="M" fixed="false" matchMax="1" matchMin="0" >Montague</simpleAssociableChoice><simpleAssociableChoice identifier="P" fixed="false" matchMax="2" matchMin="0" >Prospero</simpleAssociableChoice></associateInteraction></itemBody><responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response"/></assessmentItem>'
        },
        {
            data : item_feedback,
            comment : 'choice + feedback rules',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:m="http://www.w3.org/1998/Math/MathML" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="i14474112607310141" title="Item 13" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.1.0-sprint12" ><responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="identifier" ><correctResponse><value><![CDATA[ choice_1 ]]></value></correctResponse></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" /><outcomeDeclaration identifier="FEEDBACK_1" cardinality="single" baseType="identifier" /><stylesheet href="style/custom/tao-user-styles.css" type="text/css" media="all" title="" /><itemBody><div class="grid-row"><div class="col-12"><choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="0" minChoices="0" orientation="vertical" ><prompt >Question ?</prompt><simpleChoice identifier="choice_1" fixed="false" showHide="show" >choice #1</simpleChoice><simpleChoice identifier="choice_2" fixed="false" showHide="show" >choice #2</simpleChoice><simpleChoice identifier="choice_3" fixed="false" showHide="show" >choice #3</simpleChoice></choiceInteraction></div></div></itemBody><responseProcessing><responseCondition><responseIf><match><variable identifier="RESPONSE" /><correct identifier="RESPONSE" /></match><setOutcomeValue identifier="SCORE"><sum><variable identifier="SCORE" /><baseValue baseType="integer">1</baseValue></sum></setOutcomeValue></responseIf></responseCondition><responseCondition><responseIf><match><variable identifier="RESPONSE" /><correct identifier="RESPONSE" /></match><setOutcomeValue identifier="FEEDBACK_1"><baseValue baseType="identifier">feedbackModal_1</baseValue></setOutcomeValue></responseIf><responseElse><setOutcomeValue identifier="FEEDBACK_1"><baseValue baseType="identifier">feedbackModal_2</baseValue></setOutcomeValue></responseElse></responseCondition></responseProcessing><modalFeedback identifier="feedbackModal_1" outcomeIdentifier="FEEDBACK_1" showHide="show" title="modal feedback title" ><div class="x-tao-wrapper x-tao-relatedOutcome-RESPONSE">Right</div></modalFeedback><modalFeedback identifier="feedbackModal_2" outcomeIdentifier="FEEDBACK_1" showHide="show" title="modal feedback title" ><div class="x-tao-wrapper x-tao-relatedOutcome-RESPONSE">Wrong</div></modalFeedback></assessmentItem>'
        },
        {
            data : item_feedback_complex,
            comment : 'choice + more complex feedback rules',
            xml : '<?xml version="1.0" encoding="UTF-8"?><assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:m="http://www.w3.org/1998/Math/MathML" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd" identifier="i14474112607310141" title="Item 15" label="" adaptive="false" timeDependent="false" toolName="TAO" toolVersion="3.1.0-sprint12" ><responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="identifier" ><correctResponse><value><![CDATA[ choice_1 ]]></value></correctResponse></responseDeclaration><outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" /><outcomeDeclaration identifier="FEEDBACK_1" cardinality="single" baseType="identifier" /><outcomeDeclaration identifier="FEEDBACK_2" cardinality="single" baseType="identifier" /><stylesheet href="style/custom/tao-user-styles.css" type="text/css" media="all" title="" /><itemBody><div class="grid-row"><div class="col-12"><choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="0" minChoices="0" orientation="vertical" ><prompt >Question ?</prompt><simpleChoice identifier="choice_1" fixed="false" showHide="show" >choice #1</simpleChoice><simpleChoice identifier="choice_2" fixed="false" showHide="show" >choice #2</simpleChoice><simpleChoice identifier="choice_3" fixed="false" showHide="show" >choice #3</simpleChoice></choiceInteraction></div></div></itemBody><responseProcessing><responseCondition><responseIf><match><variable identifier="RESPONSE" /><correct identifier="RESPONSE" /></match><setOutcomeValue identifier="SCORE"><sum><variable identifier="SCORE" /><baseValue baseType="integer">1</baseValue></sum></setOutcomeValue></responseIf></responseCondition><responseCondition><responseIf><not><match><variable identifier="RESPONSE" /><correct identifier="RESPONSE" /></match></not><setOutcomeValue identifier="FEEDBACK_1"><baseValue baseType="identifier">feedbackModal_1</baseValue></setOutcomeValue></responseIf></responseCondition><responseCondition><responseIf><gt><mapResponse identifier="RESPONSE" /><baseValue baseType="float">0</baseValue></gt><setOutcomeValue identifier="FEEDBACK_2"><baseValue baseType="identifier">feedbackModal_2</baseValue></setOutcomeValue></responseIf></responseCondition></responseProcessing><modalFeedback identifier="feedbackModal_1" outcomeIdentifier="FEEDBACK_1" showHide="show" title="modal feedback title" ><div class="x-tao-wrapper x-tao-relatedOutcome-RESPONSE">Wrong</div></modalFeedback><modalFeedback identifier="feedbackModal_2" outcomeIdentifier="FEEDBACK_2" showHide="show" title="modal feedback title" ><div class="x-tao-wrapper x-tao-relatedOutcome-RESPONSE">Good</div></modalFeedback></assessmentItem>'
        }
    ];

    QUnit
        .cases(items)
        .asyncTest('xml rendering', function(sample, assert){

            var loader = new Loader(),
                renderer = new Renderer({
                    shuffleChoices : false,
                    runtimeContext : {
                        runtime_base_www : '/taoQtiItem/test/samples/test_base_www/',
                        root_url : '',
                        debug : true
                    }
                });

            loader.loadItemData(sample.data, function(item){

                assert.ok(Element.isA(item, 'assessmentItem'), sample.data.identifier + ' item loaded');

                renderer.load(function(){

                    QUnit.start();
                    item.setRenderer(renderer);
                    var xml = item.render();
                    xml = minXml(xml);
                    assert.equal(xml, sample.xml, 'xml equals ' + sample.comment);
                    
                }, this.getLoadedClasses());

            });

        });

});