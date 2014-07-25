#!/bin/sh

sudo cp -a 06Languages.csv /usr/lib/mysql/06Languages.csv

mysqlimport -u root -p ultimate_rdf2db /usr/lib/mysql/06Languages.csv --columns=uri,05label,05comment,06level --fields-enclosed-by='"' --fields-terminated-by=';' --fields-escaped-by='\'

