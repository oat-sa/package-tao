SELECT 
  S1.subject AS uri
  , S2.object AS `5#label`
  , S3.object as `5#comment`
  , S4.object as `6#level`

FROM statements S1
LEFT JOIN statements S2 
  ON S1.subject = S2.subject 
  AND S2.predicate = "http://www.w3.org/2000/01/rdf-schema#label"
LEFT JOIN statements S3 
  ON S1.subject = S3.subject
  AND S3.predicate = "http://www.w3.org/2000/01/rdf-schema#comment"
LEFT JOIN statements S4 
  ON S1.subject = S4.subject
  AND S4.predicate = "http://www.tao.lu/Ontologies/TAO.rdf#level"

WHERE 
S1.object = "http://www.tao.lu/Ontologies/TAO.rdf#Languages"
AND S1.predicate = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type"

