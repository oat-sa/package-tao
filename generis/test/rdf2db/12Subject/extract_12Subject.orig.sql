SELECT 
  S1.subject AS uri
  , S2.object AS `5#label`
  , S3.object as `7#userDefLg`
  , S4.object as `7#login`
  , S5.object as `7#password`
  , S6.object as `7#userUILg`
  , S7.object as `7#userMail`
  , S8.object as `7#userFirstName`
  , S9.object as `7#userLastName`

FROM statements S1

LEFT JOIN statements S2 
  ON S1.subject = S2.subject 
  AND S2.predicate = "http://www.w3.org/2000/01/rdf-schema#label"
LEFT JOIN statements S3 
  ON S1.subject = S3.subject
  AND S3.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userDefLg"
LEFT JOIN statements S4 
  ON S1.subject = S4.subject
  AND S4.predicate = "http://www.tao.lu/Ontologies/generis.rdf#login"
LEFT JOIN statements S5 
  ON S1.subject = S5.subject
  AND S5.predicate = "http://www.tao.lu/Ontologies/generis.rdf#password"
LEFT JOIN statements S6 
  ON S1.subject = S6.subject
  AND S6.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userUILg"
LEFT JOIN statements S7 
  ON S1.subject = S7.subject
  AND S7.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userMail"
LEFT JOIN statements S8 
  ON S1.subject = S8.subject
  AND S8.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userFirstName"
LEFT JOIN statements S9 
  ON S1.subject = S9.subject
  AND S9.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userLastName"

WHERE 
( S1.object = "http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole"
OR S1.object = "http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject" )
AND S1.predicate = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type"
