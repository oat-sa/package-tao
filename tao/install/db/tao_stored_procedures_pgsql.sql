-- sequence_uri_provider_uri_sequence_seq --> name of the sequence for currval()
-- it is generated automatically by postgres
CREATE OR REPLACE FUNCTION generis_sequence_uri_provider(modeluri varchar)
RETURNS varchar
LANGUAGE plpgsql
VOLATILE
AS $$
DECLARE uri varchar;
DECLARE stamp int;
BEGIN
	INSERT INTO sequence_uri_provider DEFAULT VALUES;
	SELECT INTO stamp extract(epoch FROM now())::int;
	uri := modeluri || 'i' || stamp || FLOOR(RANDOM() * 10000) || currval('sequence_uri_provider_uri_sequence_seq');
	RETURN uri;
END
$$;
