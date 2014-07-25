#!/bin/sh

sudo cp -a 12Subject.csv /usr/lib/mysql/12Subject.csv

mysqlimport -u root -p ultimate_rdf2db /usr/lib/mysql/12Subject.csv --columns=uri,07userDefLg,07login,07password,07userUILg,07userMail,07userFirstName,07userLastName --fields-enclosed-by='"' --fields-terminated-by=';' --fields-escaped-by='\'


sudo cp -a 12Subject_tr.csv /usr/lib/mysql/12Subject_tr.csv

mysqlimport -u root -p ultimate_rdf2db /usr/lib/mysql/12Subject_tr.csv --columns=uri,l_language,06label,06comment --fields-enclosed-by='"' --fields-terminated-by=';' --fields-escaped-by='\'

