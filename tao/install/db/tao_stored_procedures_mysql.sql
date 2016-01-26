DROP FUNCTION IF EXISTS generis_sequence_uri_provider;

DELIMITER $$
CREATE
DEFINER = CURRENT_USER
FUNCTION generis_sequence_uri_provider (modelUri VARCHAR(255))
RETURNS VARCHAR(255)
NOT DETERMINISTIC
READS SQL DATA
SQL SECURITY INVOKER
BEGIN
	DECLARE uri VARCHAR(255);
	INSERT INTO sequence_uri_provider (uri_sequence) VALUES (null);
	SELECT CONCAT(modelUri, 'i' , UNIX_TIMESTAMP(), FLOOR(RAND() * 10000), LAST_INSERT_ID()) INTO uri;
	DELETE FROM sequence_uri_provider;
	RETURN uri;
END;
$$
DELIMITER ;