<?php

use qtism\data\storage\xml\XmlDocument;

require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Integration test of the {@link taoQtiTest_models_classes_QtiTestConverter} class.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package taoQtiTest
 
 */
class QtiTestConverterTest extends TaoPhpUnitTestRunner {
    
//     "rubricBlocks" : [ { "content" : [  ],
//                    "rubricBlock" : { "content" : [  ],
//                        "qti-type" : "rubricBlock",
//                        "views" : [ 1 ]
//                      },
//                    "views" : [ "" ]
//                  } ],
    
    /**
     * Data provider 
     * @return array[] the parameters
     */
    public function dataProvider(){
        
        $testPath = dirname(__FILE__) . '/data/qtitest.xml';
       $json = '{
   "qti-type":"assessmentTest",
   "identifier":"testId",
   "title":"testTitle",
   "toolName":"",
   "toolVersion":"",
   "outcomeDeclarations":[],
   "testParts":[
      {
         "qti-type":"testPart",
         "identifier":"testPartId",
         "navigationMode":0,
         "submissionMode":0,
         "preConditions":[],
         "branchRules":[],
         "itemSessionControl":{
            "qti-type":"itemSessionControl",
            "maxAttempts":0,
            "showFeedback":false,
            "allowReview":true,
            "showSolution":false,
            "allowComment":false,
            "validateResponses":false,
            "allowSkipping":true
         },
         "assessmentSections":[
            {
               "qti-type":"assessmentSection",
               "title":"assessmentSectionTitle",
               "visible":false,
               "keepTogether":true,
               "ordering":{
                  "qti-type":"ordering",
                  "shuffle":true
               },
               "rubricBlocks":[],
               "sectionParts":[
                  {
                     "qti-type":"assessmentItemRef",
                     "href":"http:\/\/tao.localdomain\/bertao.rdf#i137968191265683",
                     "categories":[],
                     "variableMappings":{},
                     "weights":[],
                     "templateDefaults":{},
                     "identifier":"astronomy",
                     "required":false,
                     "fixed":false,
                     "preConditions":[],
                     "branchRules":[]
                  },
                  {
                     "qti-type":"assessmentItemRef",
                     "href":"http:\/\/tao.localdomain\/bertao.rdf#i137968191389526",
                     "categories":[],
                     "variableMappings":{},
                     "weights":[],
                     "templateDefaults":{},
                     "identifier":"elections-in-the-united-states-2004",
                     "required":false,
                     "fixed":false,
                     "preConditions":[],
                     "branchRules":[]
                  },
                  {
                     "qti-type":"assessmentItemRef",
                     "href":"http:\/\/tao.localdomain\/bertao.rdf#i137968191388459",
                     "categories":[],
                     "variableMappings":{},
                     "weights":[],
                     "templateDefaults":{},
                     "identifier":"periods-of-history",
                     "required":false,
                     "fixed":false,
                     "preConditions":[],
                     "branchRules":[]
                  },
                  {
                     "qti-type":"assessmentItemRef",
                     "href":"http:\/\/tao.localdomain\/bertao.rdf#i1379681914588612",
                     "categories":[],
                     "variableMappings":{},
                     "weights":[],
                     "templateDefaults":{},
                     "identifier":"space-shuttle-30-years-of-adventure",
                     "required":false,
                     "fixed":false,
                     "preConditions":[],
                     "branchRules":[]
                  }
               ],
               "identifier":"assessmentSectionId",
               "required":true,
               "fixed":false,
               "preConditions":[],
               "branchRules":[]
            }
         ],
         "testFeedbacks":[]
      }
   ],
   "testFeedbacks":[]
}';
        
        return array(
            array($testPath, str_replace(array(' ', "\n", "\t"), '', $json))
        );
    }
    
    /**
     * Test {@link taoQtiTest_models_classes_QtiTestConverter::toJson}
     * @dataProvider dataProvider
     * @param string $testPath the path of the QTI test to convert
     * @param string $expected the expected json result 
     */
    public function testToJson($testPath, $expected){
        
        $doc = new XmlDocument('2.1');
        try {
            $doc->load($testPath);
        } catch (StorageException $e) {
            $this->fail($e->getMessage());
        }
        
        $converter = new taoQtiTest_models_classes_QtiTestConverter($doc);
        $result = $converter->toJson();

        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test {@link taoQtiTest_models_classes_QtiTestConverter::fromJson}
     * @dataProvider dataProvider
     * @param string $testPath 
     * @param string $json 
     */
   public function testFromJson($testPath, $json){
        
        $doc = new XmlDocument('2.1');
        $converter = new taoQtiTest_models_classes_QtiTestConverter($doc);
        $converter->fromJson($json);
        $result = $doc->saveToString();
        $this->assertEquals($result, file_get_contents($testPath));
    }

}
