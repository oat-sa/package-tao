CREATE OR REPLACE FUNCTION date_to_unix_ts (PDate IN date) 
   RETURN NUMBER 
   IS
    l_unix_ts number;
   BEGIN
    l_unix_ts := ( PDate - date '1970-01-01' ) * 60 * 60 * 24;
    RETURN l_unix_ts;
   END;
;;
CREATE OR REPLACE FUNCTION generis_sequence_uri_provider (modelUri IN Varchar2)
  RETURN VARCHAR2
  IS
    uri Varchar2(255);
    v_id NUMBER(10,0);
  pragma autonomous_transaction;
  BEGIN
    INSERT INTO sequence_uri_provider (uri_sequence) VALUES ('')
    RETURNING uri_sequence into v_id;
    SELECT modelUri || 'i' || DATE_TO_UNIX_TS(SYSTIMESTAMP) || FLOOR(dbms_random.value(1,1000)) || v_id
    INTO uri
    FROM DUAL;
    DELETE FROM sequence_uri_provider;
    commit;
    RETURN(uri);
  END;

