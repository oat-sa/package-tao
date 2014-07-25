SELECT 
  S1.subject AS uri
  , S2.l_language AS `l_langauge`
  , S2.object AS `5#label`
  , S3.object AS `5#comment`

FROM statements S1

LEFT JOIN statements S2 
  ON S1.subject = S2.subject
  AND S2.predicate = "http://www.w3.org/2000/01/rdf-schema#label"
LEFT JOIN statements S3 
  ON S1.subject = S3.subject
  AND S3.predicate = "http://www.w3.org/2000/01/rdf-schema#comment"

WHERE 
( S1.object = "http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole"
OR S1.object = "http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject" )
AND S1.predicate = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type"
AND S2.l_language = S3.l_language

