SELECT 
  S1.subject AS uri
  , S2.object as `7#userDefLg`
  , S3.object as `7#login`
  , S4.object as `7#password`
  , S5.object as `7#userUILg`
  , S6.object as `7#userMail`
  , S7.object as `7#userFirstName`
  , S8.object as `7#userLastName`

FROM statements S1

INNER JOIN statements S2 
  ON S1.subject = S2.subject
  AND S2.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userDefLg"
INNER JOIN statements S3 
  ON S1.subject = S3.subject
  AND S3.predicate = "http://www.tao.lu/Ontologies/generis.rdf#login"
INNER JOIN statements S4 
  ON S1.subject = S4.subject
  AND S4.predicate = "http://www.tao.lu/Ontologies/generis.rdf#password"
INNER JOIN statements S5 
  ON S1.subject = S5.subject
  AND S5.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userUILg"
INNER JOIN statements S6 
  ON S1.subject = S6.subject
  AND S6.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userMail"
INNER JOIN statements S7 
  ON S1.subject = S7.subject
  AND S7.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userFirstName"
INNER JOIN statements S8 
  ON S1.subject = S8.subject
  AND S8.predicate = "http://www.tao.lu/Ontologies/generis.rdf#userLastName"

WHERE 
S1.object = "http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject"
AND S1.predicate = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type"
